    <?php

    namespace App\Services;

    use Illuminate\Http\UploadedFile;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class GroqQuizService
    {
        protected string $apiKey;
        protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
        protected string $model = 'llama-3.3-70b-versatile';

        public function __construct()
        {
            $this->apiKey = config('services.groq.api_key', '');
        }

        public function isConfigured(): bool
        {
            return !empty($this->apiKey);
        }

        public function extractTextFromFile(UploadedFile $file): array
        {
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();

            try {
                if ($extension === 'pdf' || $mimeType === 'application/pdf') {
                    return $this->extractTextFromPdf($file);
                }

                if (in_array($extension, ['doc', 'docx']) || str_contains($mimeType, 'word')) {
                    return $this->extractTextFromWord($file);
                }

                if ($extension === 'txt' || $mimeType === 'text/plain') {
                    return [
                        'success' => true,
                        'text' => file_get_contents($file->getRealPath()),
                        'type' => 'text',
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Unsupported file type. Please upload PDF, Word document, or text files.',
                ];
            } catch (\Exception $e) {
                Log::error('File extraction error', ['message' => $e->getMessage()]);
                return [
                    'success' => false,
                    'error' => 'Failed to extract text from file: ' . $e->getMessage(),
                ];
            }
        }

        protected function extractTextFromPdf(UploadedFile $file): array
        {
            $text = '';
            $filePath = $file->getRealPath();

            if (function_exists('exec')) {
                $output = [];
                $tempFile = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.txt';
                @exec("pdftotext -layout \"{$filePath}\" \"{$tempFile}\" 2>&1", $output, $returnCode);
                
                if ($returnCode === 0 && file_exists($tempFile)) {
                    $text = file_get_contents($tempFile);
                    @unlink($tempFile);
                    
                    if (!empty(trim($text))) {
                        return [
                            'success' => true,
                            'text' => $text,
                            'type' => 'pdf',
                        ];
                    }
                }
            }

            $content = file_get_contents($filePath);
            
            if (preg_match_all('/\((.*?)\)/', $content, $matches)) {
                $extractedParts = [];
                foreach ($matches[1] as $match) {
                    $decoded = $this->decodePdfString($match);
                    if (!empty(trim($decoded)) && strlen($decoded) > 1) {
                        $extractedParts[] = $decoded;
                    }
                }
                $text = implode(' ', $extractedParts);
            }

            if (!empty(trim($text)) && strlen($text) > 50) {
                return [
                    'success' => true,
                    'text' => $this->cleanExtractedText($text),
                    'type' => 'pdf',
                ];
            }

            return [
                'success' => false,
                'error' => 'Could not extract text from this PDF. It may be scanned/image-based. Please try a text-based PDF or Word document.',
            ];
        }

        protected function decodePdfString(string $str): string
        {
            $str = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $str);
            $str = preg_replace('/\\\\([0-7]{1,3})/', '', $str);
            $str = stripslashes($str);
            return $str;
        }

        protected function extractTextFromWord(UploadedFile $file): array
        {
            $extension = strtolower($file->getClientOriginalExtension());
            $filePath = $file->getRealPath();

            if ($extension === 'docx') {
                return $this->extractTextFromDocx($filePath);
            }

            if (function_exists('exec')) {
                $output = [];
                @exec("antiword \"{$filePath}\" 2>&1", $output, $returnCode);
                
                if ($returnCode === 0 && !empty($output)) {
                    return [
                        'success' => true,
                        'text' => implode("\n", $output),
                        'type' => 'word',
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Could not extract text from .doc file. Please convert it to .docx format and try again.',
            ];
        }

        protected function extractTextFromDocx(string $filePath): array
        {
            if (!class_exists('ZipArchive')) {
                return [
                    'success' => false,
                    'error' => 'ZIP extension is not available for Word document processing.',
                ];
            }

            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $content = $zip->getFromName('word/document.xml');
                $zip->close();

                if ($content !== false) {
                    $text = strip_tags($content);
                    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
                    $text = preg_replace('/\s+/', ' ', $text);
                    $text = trim($text);

                    return [
                        'success' => true,
                        'text' => $text,
                        'type' => 'word',
                    ];
                }
            }

            return [
                'success' => false,
                'error' => 'Could not read the Word document. The file may be corrupted.',
            ];
        }

        protected function cleanExtractedText(string $text): string
        {
            $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
            $text = preg_replace('/\s+/', ' ', $text);
            return trim($text);
        }

        public function generateQuizFromDocument(array $params): array
        {
            $documentContent = $params['document_content'] ?? '';
            $mode = $params['mode'] ?? 'context';
            $questionCount = (int) ($params['question_count'] ?? 10);
            $difficulty = $params['difficulty'] ?? 'medium';
            $questionTypes = $params['question_types'] ?? ['multiple_choice'];
            $additionalInstructions = $params['additional_instructions'] ?? '';
            $subjectContext = $params['subject_context'] ?? '';

            if (empty($documentContent)) {
                return [
                    'success' => false,
                    'error' => 'No document content provided.',
                ];
            }

            $questionTypeStr = implode(' and ', $questionTypes);

            if ($mode === 'extract_questions') {
                return $this->extractQuestionsFromContent($documentContent);
            }

            $systemPrompt = 'You are an expert educational quiz creator. Generate quiz questions based on the provided document content.

    IMPORTANT: You must respond with ONLY valid JSON. No explanations, no markdown, no code blocks - just the raw JSON object.

    The JSON structure must be:
    {
    "questions": [
        {
        "type": "multiple_choice",
        "question_text": "The question here?",
        "points": 1,
        "choices": [
            {"text": "Option A", "is_correct": false},
            {"text": "Option B", "is_correct": true},
            {"text": "Option C", "is_correct": false},
            {"text": "Option D", "is_correct": false}
        ]
        },
        {
        "type": "essay",
        "question_text": "Essay question here?",
        "points": 5
        }
    ]
    }

    Rules:
    - For multiple_choice: exactly 4 choices, exactly one is_correct: true
    - For essay: no choices field, higher points (3-5)
    - All questions must be based on the provided document content
    - Questions should test comprehension and understanding of the material
    - Questions should match the specified difficulty level
    - Use clear, unambiguous language';

            $userPrompt = "Generate a quiz based on the following document content:\n\n";
            $userPrompt .= "--- DOCUMENT CONTENT ---\n{$documentContent}\n--- END DOCUMENT ---\n\n";
            $userPrompt .= "Quiz specifications:\n";
            $userPrompt .= "- Number of questions: {$questionCount}\n";
            $userPrompt .= "- Difficulty level: {$difficulty}\n";
            $userPrompt .= "- Question types: {$questionTypeStr}\n";

            if (!empty($subjectContext)) {
                $userPrompt .= "- Subject/Course context: {$subjectContext}\n";
            }

            if (!empty($additionalInstructions)) {
                $userPrompt .= "- Additional instructions: {$additionalInstructions}\n";
            }

            $userPrompt .= "\nGenerate questions that test understanding of this content. Respond with ONLY the JSON object.";

            return $this->callGroqApi($systemPrompt, $userPrompt);
        }

        protected function extractQuestionsFromContent(string $content): array
        {
            $systemPrompt = 'You are an expert at analyzing educational documents. The user has provided a document that contains quiz/test questions.

    Your task:
    1. Extract all questions from the document
    2. If questions already have answer choices, preserve them and identify the correct answer
    3. If questions do not have answers, create appropriate answer choices with correct answers
    4. Format everything as a proper quiz

    IMPORTANT: You must respond with ONLY valid JSON. No explanations, no markdown, no code blocks.

    The JSON structure must be:
    {
    "questions": [
        {
        "type": "multiple_choice",
        "question_text": "The question here?",
        "points": 1,
        "choices": [
            {"text": "Option A", "is_correct": false},
            {"text": "Option B", "is_correct": true},
            {"text": "Option C", "is_correct": false},
            {"text": "Option D", "is_correct": false}
        ]
        },
        {
        "type": "essay",
        "question_text": "Essay question here?",
        "points": 5
        }
    ]
    }

    Rules:
    - Extract ALL questions found in the document
    - For multiple choice: exactly 4 choices, exactly one is_correct: true
    - If original has fewer than 4 choices, add plausible distractors
    - If no correct answer is marked, determine the correct answer based on the content
    - For open-ended/essay questions, use type "essay"
    - Preserve the original question wording as much as possible';

            $userPrompt = "Extract and process all questions from this document:\n\n";
            $userPrompt .= "--- DOCUMENT CONTENT ---\n{$content}\n--- END DOCUMENT ---\n\n";
            $userPrompt .= "Extract all questions, provide answers where missing, and format as a quiz. Respond with ONLY the JSON object.";

            return $this->callGroqApi($systemPrompt, $userPrompt);
        }

        public function generateQuiz(array $params): array
        {
            $topic = $params['topic'] ?? 'General Knowledge';
            $questionCount = (int) ($params['question_count'] ?? 5);
            $difficulty = $params['difficulty'] ?? 'medium';
            $questionTypes = $params['question_types'] ?? ['multiple_choice'];
            $additionalInstructions = $params['additional_instructions'] ?? '';
            $subjectContext = $params['subject_context'] ?? '';
            $gradeLevel = $params['grade_level'] ?? '';

            $questionTypeStr = implode(' and ', $questionTypes);
            
            $systemPrompt = 'You are an expert educational quiz creator. Generate quiz questions that are clear, accurate, and educationally valuable.

    IMPORTANT: You must respond with ONLY valid JSON. No explanations, no markdown, no code blocks - just the raw JSON object.

    The JSON structure must be:
    {
    "questions": [
        {
        "type": "multiple_choice",
        "question_text": "The question here?",
        "points": 1,
        "choices": [
            {"text": "Option A", "is_correct": false},
            {"text": "Option B", "is_correct": true},
            {"text": "Option C", "is_correct": false},
            {"text": "Option D", "is_correct": false}
        ]
        },
        {
        "type": "essay",
        "question_text": "Essay question here?",
        "points": 5
        }
    ]
    }

    Rules:
    - For multiple_choice: exactly 4 choices, exactly one is_correct: true
    - For essay: no choices field, higher points (3-5)
    - All questions must be factually accurate
    - Questions should match the specified difficulty level
    - Use clear, unambiguous language';

            $userPrompt = "Generate a quiz with the following specifications:\n\n";
            $userPrompt .= "- Topic: {$topic}\n";
            $userPrompt .= "- Number of questions: {$questionCount}\n";
            $userPrompt .= "- Difficulty level: {$difficulty}\n";
            $userPrompt .= "- Question types: {$questionTypeStr}\n";
            
            if (!empty($subjectContext)) {
                $userPrompt .= "- Subject/Course context: {$subjectContext}\n";
            }
            
            if (!empty($gradeLevel)) {
                $userPrompt .= "- Grade/Education level: {$gradeLevel}\n";
            }
            
            if (!empty($additionalInstructions)) {
                $userPrompt .= "- Additional instructions: {$additionalInstructions}\n";
            }

            $userPrompt .= "\nGenerate the quiz now. Remember: respond with ONLY the JSON object, nothing else.";

            $result = $this->callGroqApi($systemPrompt, $userPrompt);
            
            if ($result['success']) {
                $result['meta'] = [
                    'topic' => $topic,
                    'difficulty' => $difficulty,
                    'question_count' => count($result['questions']),
                ];
            }

            return $result;
        }

        protected function callGroqApi(string $systemPrompt, string $userPrompt): array
        {
            try {
                $maxLength = 40000;
                if (strlen($userPrompt) > $maxLength) {
                    $userPrompt = substr($userPrompt, 0, $maxLength) . "\n\n[Content truncated due to length...]";
                }

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])->timeout(90)->post($this->baseUrl, [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 4096,
                ]);

                if (!$response->successful()) {
                    $errorBody = $response->json();
                    $errorMessage = $errorBody['error']['message'] ?? 'Unknown API error';
                    
                    Log::error('Groq API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    
                    if ($response->status() === 401) {
                        return [
                            'success' => false,
                            'error' => 'Invalid Groq API key. Please check your GROQ_API_KEY in the .env file.',
                        ];
                    }
                    
                    if ($response->status() === 429) {
                        return [
                            'success' => false,
                            'error' => 'Rate limit exceeded. Please wait a moment and try again.',
                        ];
                    }
                    
                    return [
                        'success' => false,
                        'error' => 'API Error: ' . $errorMessage,
                    ];
                }

                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                return $this->parseQuizResponse($content);

            } catch (\Exception $e) {
                Log::error('Groq API exception', ['message' => $e->getMessage()]);
                return [
                    'success' => false,
                    'error' => 'Connection error: ' . $e->getMessage(),
                ];
            }
        }

        protected function parseQuizResponse(string $content): array
        {
            $content = trim($content);
            if (str_starts_with($content, '```json')) {
                $content = substr($content, 7);
            } elseif (str_starts_with($content, '```')) {
                $content = substr($content, 3);
            }
            if (str_ends_with($content, '```')) {
                $content = substr($content, 0, -3);
            }
            $content = trim($content);

            $quizData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse Groq response as JSON', [
                    'content' => $content,
                    'error' => json_last_error_msg(),
                ]);
                return [
                    'success' => false,
                    'error' => 'Failed to parse AI response. Please try again.',
                ];
            }

            if (!isset($quizData['questions']) || !is_array($quizData['questions'])) {
                return [
                    'success' => false,
                    'error' => 'Invalid quiz format received from AI.',
                ];
            }

            $validatedQuestions = $this->validateAndCleanQuestions($quizData['questions']);

            if (empty($validatedQuestions)) {
                return [
                    'success' => false,
                    'error' => 'No valid questions could be generated. Please try again with a different topic.',
                ];
            }

            return [
                'success' => true,
                'questions' => $validatedQuestions,
            ];
        }

        protected function validateAndCleanQuestions(array $questions): array
        {
            $validated = [];

            foreach ($questions as $index => $q) {
                $type = $q['type'] ?? 'multiple_choice';
                $questionText = trim($q['question_text'] ?? '');
                $points = (int) ($q['points'] ?? ($type === 'essay' ? 5 : 1));

                if (empty($questionText)) {
                    continue;
                }

                $cleaned = [
                    'type' => in_array($type, ['multiple_choice', 'essay']) ? $type : 'multiple_choice',
                    'question_text' => $questionText,
                    'points' => max(1, $points),
                    'position' => $index,
                ];

                if ($cleaned['type'] === 'multiple_choice') {
                    $choices = $q['choices'] ?? [];
                    $cleanedChoices = [];
                    $hasCorrect = false;

                    foreach ($choices as $choice) {
                        $choiceText = trim($choice['text'] ?? '');
                        if (empty($choiceText)) {
                            continue;
                        }
                        $isCorrect = (bool) ($choice['is_correct'] ?? false);
                        if ($isCorrect) {
                            $hasCorrect = true;
                        }
                        $cleanedChoices[] = [
                            'text' => $choiceText,
                            'is_correct' => $isCorrect,
                        ];
                    }

                    if (count($cleanedChoices) < 2) {
                        continue;
                    }

                    if (!$hasCorrect && count($cleanedChoices) > 0) {
                        $cleanedChoices[0]['is_correct'] = true;
                    }

                    $cleaned['choices'] = $cleanedChoices;
                }

                $validated[] = $cleaned;
            }

            return $validated;
        }
    }
