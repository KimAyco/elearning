

<?php $__env->startSection('title', 'Admin Tools - School Portal'); ?>

<?php $__env->startSection('content'); ?>
<div class="app-shell">
    <?php echo $__env->make('tenant.partials.sidebar', ['active' => 'admin'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Admin Tools</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar"><?php echo e(strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1))); ?></div>
                    <span><?php echo e(auth()->user()->full_name ?? 'User'); ?></span>
                </div>
            </div>
        </header>

        <main class="page-body">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="<?php echo e(url('/tenant/dashboard')); ?>">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Admin Tools</span>
                </div>
                <h1>School Admin & Curriculum Management</h1>
                <p>Manage academic structure, courses, subjects, offerings, and school staff assignments.</p>
            </div>

            <?php if(session('status')): ?>
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span><?php echo e(session('status')); ?></span>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span><?php echo e($errors->first()); ?></span>
                </div>
            <?php endif; ?>

            
            <div data-tabs="admin-tabs">
            <div class="tabs">
                <a class="tab-btn <?php echo e(($activeAdminTab ?? 'structure') === 'structure' ? 'active' : ''); ?>" data-tab="structure" href="<?php echo e(url('/tenant/admin')); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Academic Structure
                </a>
                <a class="tab-btn <?php echo e(($activeAdminTab ?? 'structure') === 'subjects' ? 'active' : ''); ?>" data-tab="subjects" href="<?php echo e(url('/tenant/admin/subjects')); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Subjects
                </a>
                <a class="tab-btn <?php echo e(($activeAdminTab ?? 'structure') === 'classes' ? 'active' : ''); ?>" data-tab="classes" href="<?php echo e(url('/tenant/admin/classes')); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M8 2v4"></path><path d="M16 2v4"></path><path d="M3 10h18"></path>
                    </svg>
                    Classes
                </a>
                <a class="tab-btn <?php echo e(($activeAdminTab ?? 'structure') === 'roles' ? 'active' : ''); ?>" data-tab="roles" href="<?php echo e(url('/tenant/admin/roles')); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    School Staffs
                </a>
                <a class="tab-btn <?php echo e(($activeAdminTab ?? 'structure') === 'students' ? 'active' : ''); ?>" data-tab="students" href="<?php echo e(url('/tenant/admin/students')); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    Students
                </a>
            </div>

            
            <div class="tab-panel <?php echo e(($activeAdminTab ?? 'structure') === 'structure' ? 'active' : ''); ?>" data-panel="structure">
                <a
                    href="<?php echo e(url('/tenant/admin/structure/add')); ?>"
                    class="btn primary admin-structure-fab"
                    title="Create new departments and programs"
                >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/><path d="M12 11v6"/><path d="M9 14h6"/>
                        </svg>
                        Add Department &amp; Program
                </a>

                
                <div class="card structure-card">
                    <div class="structure-card-header">
                        <h2 class="structure-card-title">Academic Structure</h2>
                        <div class="structure-card-meta">
                            <span class="structure-meta-pill"><?php echo e($departments->count()); ?> departments</span>
                            <span class="structure-meta-pill"><?php echo e($programs->count()); ?> programs</span>
                        </div>
                    </div>
                    <div class="tree-wrap structure-tree">
                        <?php
                            $schoolName = $colleges->first()->name ?? 'School';
                        ?>
                        <details class="tree-school" open>
                            <summary class="tree-school-summary">
                                <span class="tree-node-dot school"></span>
                                <span class="tree-school-name"><?php echo e($schoolName); ?></span>
                            </summary>
                            <div class="tree-children">
                                <?php $__empty_1 = true; $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <details class="tree-department" open>
                                        <summary class="tree-department-summary">
                                            <span class="tree-node-dot department"></span>
                                            <span class="tree-department-name"><?php echo e($d->name); ?></span>
                                            <span class="tree-department-count"><?php echo e($programs->where('department_id', $d->id)->count()); ?> programs</span>
                                        </summary>
                                        <div class="tree-program-list">
                                            <?php $__empty_2 = true; $__currentLoopData = $programs->where('department_id', $d->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                <?php
                                                    $programPanelId = 'program-plan-' . $p->id;
                                                    $programItems = $curriculumItems
                                                        ->where('program_id', $p->id)
                                                        ->sortBy(fn ($item) => sprintf('%02d-%06d-%06d', $item->year_level, $item->semester_id, $item->id));
                                                    $programSubjects = $subjects->filter(function ($sub) use ($d) {
                                                        return $sub->department_id === null || (int) $sub->department_id === (int) $d->id;
                                                    });
                                                ?>
                                                <button
                                                    type="button"
                                                    class="tree-program-item program-plan-toggle"
                                                    data-target="<?php echo e($programPanelId); ?>"
                                                    aria-expanded="false"
                                                >
                                                    <span class="tree-node-dot program"></span>
                                                    <span class="tree-program-code"><?php echo e($p->code); ?></span>
                                                    <span class="tree-program-name"><?php echo e($p->name); ?></span>
                                                    <span class="tree-program-badge"><?php echo e(ucfirst($p->degree_level)); ?></span>
                                                    <span class="tree-program-plan">Plan</span>
                                                </button>
                                                <div id="<?php echo e($programPanelId); ?>" class="program-plan-modal" data-program-id="<?php echo e($p->id); ?>" hidden>
                                                    <div class="program-plan-dialog" role="dialog" aria-modal="true" aria-label="Program Curriculum Editor">
                                                        <div class="program-plan-head">
                                                            <h3 class="program-plan-title">Program Curriculum: <?php echo e($p->code); ?> - <?php echo e($p->name); ?></h3>
                                                            <button type="button" class="btn sm ghost program-plan-close" data-close-target="<?php echo e($programPanelId); ?>">Close</button>
                                                        </div>
                                                        <form method="post" action="<?php echo e(url('/tenant/admin/curriculum-items')); ?>" class="stack program-plan-add-form">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="program_id" value="<?php echo e($p->id); ?>">
                                                            <div class="inline">
                                                                <div class="form-group">
                                                                    <label>Year Level</label>
                                                                    <select name="year_level" required>
                                                                        <?php for($y=1; $y<=6; $y++): ?>
                                                                            <option value="<?php echo e($y); ?>">Year <?php echo e($y); ?></option>
                                                                        <?php endfor; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Semester</label>
                                                                    <select name="semester_id" required>
                                                                        <?php $__empty_3 = true; $__currentLoopData = $fixedSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixedSemester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_3 = false; ?>
                                                                            <?php
                                                                                $fixedTermCode = $fixedSemester['model']->term_code ?? null;
                                                                                $fixedTermOrder = $fixedTermCode === '1ST'
                                                                                    ? 1
                                                                                    : ($fixedTermCode === '2ND'
                                                                                        ? 2
                                                                                        : ($fixedTermCode === 'SUMMER' ? 3 : 99));
                                                                            ?>
                                                                            <option value="<?php echo e($fixedSemester['model']->id); ?>" data-term-order="<?php echo e($fixedTermOrder); ?>"><?php echo e($fixedSemester['label']); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_3): ?>
                                                                            <option value="" disabled selected>No fixed semesters found</option>
                                                                        <?php endif; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Subject (From Subject Bank)</label>
                                                                <select name="subject_id" required>
                                                                    <?php $__empty_3 = true; $__currentLoopData = $programSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_3 = false; ?>
                                                                        <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->code); ?> - <?php echo e($sub->title); ?></option>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_3): ?>
                                                                        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->code); ?> - <?php echo e($sub->title); ?></option>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <button class="btn primary sm program-plan-add" type="button">Stage Subject</button>
                                                        </form>
                                                        <div class="program-plan-draft" data-plan-draft hidden>
                                                            <div class="program-plan-draft-row">
                                                                <span class="badge amber program-plan-draft-badge" data-draft-count>0 pending</span>
                                                                <div class="program-plan-draft-actions">
                                                                    <button type="button" class="btn success sm program-plan-save">Save Changes</button>
                                                                    <button type="button" class="btn sm ghost program-plan-discard">Discard Changes</button>
                                                                </div>
                                                            </div>
                                                            <ul class="program-plan-draft-list" data-draft-list></ul>
                                                        </div>

                                                        <div class="program-plan-list">
                                                            <h4 class="program-plan-subtitle">Current Planned Subjects</h4>
                                                            <?php $__empty_3 = true; $__currentLoopData = $programItems->groupBy('year_level'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year => $yearItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_3 = false; ?>
                                                                <div class="year-segment">
                                                                    <div class="year-segment-header">
                                                                        <h5>Year <?php echo e($year); ?></h5>
                                                                        <span class="badge"><?php echo e($yearItems->count()); ?> subjects</span>
                                                                    </div>
                                                                    <div class="table-wrap">
                                                                        <table>
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>Semester</th>
                                                                                    <th>Subject</th>
                                                                                    <th>Action</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <?php $previousSemesterLabel = null; ?>
                                                                                <?php foreach($yearItems as $item): ?>
                                                                                    <?php
                                                                                        $semesterLabel = (($item->semester->term_code ?? null) === '1ST')
                                                                                            ? 'First Semester'
                                                                                            : ((($item->semester->term_code ?? null) === '2ND')
                                                                                                ? 'Second Semester'
                                                                                                : ($item->semester->name ?? 'N/A'));
                                                                                    ?>
                                                                                    <?php if($previousSemesterLabel !== null && $previousSemesterLabel !== $semesterLabel): ?>
                                                                                        <tr class="semester-separator-row">
                                                                                            <td colspan="3">
                                                                                                <div class="semester-separator">
                                                                                                    <span><?php echo e($semesterLabel); ?></span>
                                                                                                </div>
                                                                                            </td>
                                                                                        </tr>
                                                                                    <?php endif; ?>
                                                                                    <tr data-existing-item-id="<?php echo e($item->id); ?>">
                                                                                        <td><?php echo e($semesterLabel); ?></td>
                                                                                        <td>
                                                                                            <span class="badge blue"><?php echo e($item->subject->code ?? 'N/A'); ?></span>
                                                                                            <?php echo e($item->subject->title ?? 'N/A'); ?>

                                                                                        </td>
                                                                                        <td>
                                                                                            <form method="post" action="<?php echo e(url('/tenant/admin/curriculum-items/' . $item->id . '/remove')); ?>" class="program-plan-remove-form" data-item-id="<?php echo e($item->id); ?>">
                                                                                                <?php echo csrf_field(); ?>
                                                                                                <button class="btn danger sm program-plan-remove" type="button" data-item-id="<?php echo e($item->id); ?>">Remove</button>
                                                                                            </form>
                                                                                        </td>
                                                                                    </tr>
                                                                                    <?php ($previousSemesterLabel = $semesterLabel); ?>
                                                                                <?php endforeach; ?>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_3): ?>
                                                                <p class="text-muted">No subjects mapped yet for this program.</p>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                <div class="text-muted">No programs in this department yet.</div>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="text-muted">No departments yet. Add one above.</div>
                                <?php endif; ?>
                            </div>
                        </details>
                    </div>
                </div>
            </div>

            
            <div class="tab-panel <?php echo e(($activeAdminTab ?? 'structure') === 'subjects' ? 'active' : ''); ?>" data-panel="subjects">
                <button
                    type="button"
                    class="btn primary admin-subject-fab"
                    data-role-modal-open="subject-bank-modal"
                    title="Add a new subject to the bank"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Subject Bank
                </button>

                <div id="subject-bank-modal" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Subject Bank">
                        <div class="card-header">
                            <h2>Subject Bank</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/subjects')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Department (Optional)</label>
                                <select name="department_id">
                                    <option value="">General</option>
                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Subject Code</label>
                                    <input name="code" required placeholder="e.g. MATH101">
                                </div>
                                <div class="form-group">
                                    <label>Units</label>
                                    <input name="units" type="number" step="0.1" min="0.1" value="3" required>
                                </div>
                                <div class="form-group">
                                    <label>Price Per Course Unit (PHP)</label>
                                    <input type="text" value="<?php echo e($pricePerCourseUnit !== null ? number_format((float) $pricePerCourseUnit, 2) : 'Not set in Finance'); ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input name="title" required placeholder="e.g. College Algebra">
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Weekly Hours</label>
                                    <input name="weekly_hours" type="number" step="0.1" min="0.1" value="3" required>
                                </div>
                                <div class="form-group">
                                    <label>Duration (Weeks)</label>
                                    <input name="duration_weeks" type="number" min="1" value="9" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Prerequisites (Optional)</label>
                                <select name="prerequisite_subject_ids[]" multiple size="6">
                                    <option value="" selected>No prerequisite</option>
                                    <?php $__currentLoopData = $subjects->sortBy('code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->code); ?> - <?php echo e($sub->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <p class="text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple subjects.</p>
                            </div>
                            <button class="btn success" type="submit">Add to Subject Bank</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Subject Bank Catalog</h2>
                        <span class="badge blue"><?php echo e($subjects->count()); ?> subjects</span>
                    </div>
                    <div class="table-wrap subject-bank-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Units</th>
                                    <th>Computed Price</th>
                                    <th>Hrs/Wk</th>
                                    <th>Weeks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php $computedSubjectPrice = $pricePerCourseUnit !== null
                                        ? round((float) $s->units * (float) $pricePerCourseUnit, 2)
                                        : (float) ($s->price_per_subject ?? 0); ?>
                                    <tr class="subject-row-toggle" data-role-modal-open="subject-edit-modal-<?php echo e($s->id); ?>" tabindex="0" role="button">
                                        <td><span class="badge blue"><?php echo e($s->code); ?></span></td>
                                        <td style="font-weight:500;"><?php echo e($s->title); ?></td>
                                        <td><?php echo e($s->units); ?></td>
                                        <td>PHP <?php echo e(number_format((float) $computedSubjectPrice, 2)); ?></td>
                                        <td><?php echo e($s->weekly_hours); ?></td>
                                        <td><?php echo e($s->duration_weeks); ?> wks</td>
                                        <td><span class="badge <?php echo e($s->status === 'active' ? 'green' : 'red'); ?>"><?php echo e($s->status); ?></span></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="7" class="text-muted">No subjects in the bank yet. Add one above.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div id="subject-edit-modal-<?php echo e($s->id); ?>" class="role-form-modal" hidden>
                        <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Edit Subject">
                            <div class="card-header">
                                <h2>Edit Subject: <?php echo e($s->code); ?></h2>
                                <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                            </div>
                            <form method="post" action="<?php echo e(url('/tenant/admin/subjects/' . $s->id . '/update')); ?>" class="stack">
                                <?php echo csrf_field(); ?>
                                <div class="inline">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department_id">
                                            <option value="">General</option>
                                            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($d->id); ?>" <?php echo e((int) ($s->department_id ?? 0) === (int) $d->id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" required>
                                            <option value="active" <?php echo e($s->status === 'active' ? 'selected' : ''); ?>>Active</option>
                                            <option value="inactive" <?php echo e($s->status === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="inline">
                                    <div class="form-group">
                                        <label>Subject Code</label>
                                        <input name="code" value="<?php echo e($s->code); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input name="title" value="<?php echo e($s->title); ?>" required>
                                    </div>
                                </div>
                                <div class="inline">
                                    <div class="form-group">
                                        <label>Units</label>
                                        <input name="units" type="number" step="0.1" min="0.1" value="<?php echo e($s->units); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Price Per Course Unit (PHP)</label>
                                        <input type="text" value="<?php echo e($pricePerCourseUnit !== null ? number_format((float) $pricePerCourseUnit, 2) : 'Not set in Finance'); ?>" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Weekly Hours</label>
                                        <input name="weekly_hours" type="number" step="0.1" min="0.1" value="<?php echo e($s->weekly_hours); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Duration (Weeks)</label>
                                        <input name="duration_weeks" type="number" min="1" value="<?php echo e($s->duration_weeks); ?>" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Prerequisites (Optional)</label>
                                    <select name="prerequisite_subject_ids[]" multiple size="6">
                                        <?php $__currentLoopData = $subjects->sortBy('code'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if((int) $sub->id === (int) $s->id): ?>
                                                <?php continue; ?>
                                            <?php endif; ?>
                                            <option value="<?php echo e($sub->id); ?>" <?php echo e($s->prerequisites->contains('id', $sub->id) ? 'selected' : ''); ?>>
                                                <?php echo e($sub->code); ?> - <?php echo e($sub->title); ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div style="display:flex; justify-content:flex-end; gap:8px;">
                                    <button class="btn primary sm" type="submit">Save Subject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="tab-panel" data-panel="offerings">
                <div class="grid cols-3 mb-20">
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <div class="card-icon amber">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </div>
                                Add Academic Year
                            </h2>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/academic-years')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Name</label>
                                <input name="name" placeholder="2024-2025" required>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input name="start_date" type="date" required>
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input name="end_date" type="date" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="planned">Planned</option>
                                    <option value="active">Active</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Add Academic Year</button>
                        </form>
                    </div>

                    
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <div class="card-icon purple">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                </div>
                                Add Semester
                            </h2>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/semesters')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Academic Year</label>
                                <select name="academic_year_id" required>
                                    <?php $__empty_1 = true; $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($ay->id); ?>"><?php echo e($ay->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option value="">No academic years - create one first</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Term</label>
                                    <select name="term_code" required>
                                        <option value="1ST">1st Semester</option>
                                        <option value="2ND">2nd Semester</option>
                                        <option value="SUMMER">Summer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Name</label>
                                    <input name="name" placeholder="1st Sem 2024-2025" required>
                                </div>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input name="start_date" type="date" required>
                                </div>
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input name="end_date" type="date" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="planned">Planned</option>
                                    <option value="enrollment_open">Enrollment Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Add Semester</button>
                        </form>
                    </div>

                    
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <div class="card-icon green">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                                    </svg>
                                </div>
                                Create Subject Offering
                            </h2>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/offerings')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Semester</label>
                                <select name="semester_id" required>
                                    <?php $__empty_1 = true; $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($sem->id); ?>"><?php echo e($sem->name); ?> (<?php echo e($sem->status); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option value="">No semesters - create one first</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Subject</label>
                                <select name="subject_id" required>
                                    <?php $__empty_1 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->code); ?> - <?php echo e($sub->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option value="">No subjects - create one first</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Teacher (Optional)</label>
                                <select name="teacher_user_id">
                                    <option value="">- Assign Later -</option>
                                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($t->id); ?>"><?php echo e($t->full_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Schedule Summary</label>
                                <input name="schedule_summary" placeholder="MWF 8:00-9:00 AM">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="open">Open for Enrollment</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Create Offering</button>
                        </form>
                    </div>
                </div>

                
                <div class="card mb-20">
                    <div class="card-header">
                        <h2>
                            <div class="card-icon blue">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            Add Section to Offering
                        </h2>
                    </div>
                    <form method="post" action="<?php echo e(url('/tenant/admin/sections')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <div class="form-group" style="flex:2;">
                            <label>Subject Offering</label>
                            <select name="subject_offering_id" required>
                                <?php $__currentLoopData = $offerings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($o->id); ?>"><?php echo e($o->subject->code ?? 'Subj #'.$o->subject_id); ?> - <?php echo e($o->semester->name ?? 'Sem #'.$o->semester_id); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group" style="flex:0.5;">
                            <label>Section ID</label>
                            <input name="identifier" placeholder="A" required>
                        </div>
                        <div class="form-group" style="flex:0.5;">
                            <label>Max Capacity</label>
                            <input name="max_capacity" type="number" placeholder="40" required>
                        </div>
                        <div class="form-group" style="flex:0.5;">
                            <label>Status</label>
                            <select name="status" required>
                                <option value="open">Open</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:0 0 auto; align-self:flex-end;">
                            <button class="btn primary" type="submit">Add Section</button>
                        </div>
                    </form>
                </div>

                
                <div class="card">
                    <div class="card-header">
                        <h2>Subject Offerings</h2>
                        <span class="badge blue"><?php echo e($offerings->count()); ?></span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Semester</th>
                                    <th>Teacher</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $offerings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>#<?php echo e($o->id); ?></td>
                                        <td><span class="badge blue"><?php echo e($o->subject->code ?? 'N/A'); ?></span> <?php echo e($o->subject->title ?? 'N/A'); ?></td>
                                        <td><?php echo e($o->semester->name ?? 'N/A'); ?></td>
                                        <td><?php echo e($o->teacher->full_name ?? '-'); ?></td>
                                        <td><span class="badge <?php echo e($o->status === 'open' ? 'green' : ($o->status === 'draft' ? 'amber' : 'red')); ?>"><?php echo e($o->status); ?></span></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="5" class="text-muted">No offerings yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-panel" data-panel="planner">
                <div class="grid cols-1 mb-20">
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <div class="card-icon blue">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 9v12"/>
                                    </svg>
                                </div>
                                Plan Program Curriculum
                            </h2>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/curriculum-items')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Department</label>
                                <select id="planner_department_id">
                                    <option value="">All Departments</option>
                                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Program Offered</label>
                                <select name="program_id" id="planner_program_id" required>
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id); ?>" data-department-id="<?php echo e($p->department_id); ?>"><?php echo e($p->code); ?> - <?php echo e($p->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Year Level</label>
                                    <select name="year_level" required>
                                        <?php for($y=1; $y<=6; $y++): ?>
                                            <option value="<?php echo e($y); ?>">Year <?php echo e($y); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester_id" required>
                                        <?php $__empty_1 = true; $__currentLoopData = $fixedSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixedSemester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <?php
                                                $fixedTermCode = $fixedSemester['model']->term_code ?? null;
                                                $fixedTermOrder = $fixedTermCode === '1ST'
                                                    ? 1
                                                    : ($fixedTermCode === '2ND'
                                                        ? 2
                                                        : ($fixedTermCode === 'SUMMER' ? 3 : 99));
                                            ?>
                                            <option value="<?php echo e($fixedSemester['model']->id); ?>" data-term-order="<?php echo e($fixedTermOrder); ?>"><?php echo e($fixedSemester['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <option value="" disabled selected>No fixed semesters found</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Subject (From Subject Bank)</label>
                                <select name="subject_id" required>
                                    <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sub->id); ?>"><?php echo e($sub->code); ?> - <?php echo e($sub->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Add Subject to Plan</button>
                        </form>
                    </div>

                </div>

                <div class="card">
                    <div class="card-header">
                        <h2>Curriculum Map</h2>
                        <span class="badge blue"><?php echo e($curriculumItems->count()); ?> items</span>
                    </div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Program</th>
                                    <th>Year</th>
                                    <th>Semester</th>
                                    <th>Subject</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $curriculumItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($item->program->department->name ?? 'N/A'); ?></td>
                                        <td><span class="badge blue"><?php echo e($item->program->code ?? 'N/A'); ?></span> <?php echo e($item->program->name ?? 'N/A'); ?></td>
                                        <td>Year <?php echo e($item->year_level); ?></td>
                                        <td>
                                            <?php if(($item->semester->term_code ?? null) === '1ST'): ?>
                                                First Semester
                                            <?php elseif(($item->semester->term_code ?? null) === '2ND'): ?>
                                                Second Semester
                                            <?php else: ?>
                                                <?php echo e($item->semester->name ?? 'N/A'); ?>

                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge"><?php echo e($item->subject->code ?? 'N/A'); ?></span> <?php echo e($item->subject->title ?? 'N/A'); ?></td>
                                        <td>
                                            <form method="post" action="<?php echo e(url('/tenant/admin/curriculum-items/' . $item->id . '/remove')); ?>">
                                                <?php echo csrf_field(); ?>
                                                <button class="btn danger sm" type="submit">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="6" class="text-muted">No curriculum items yet. Add one from the planner above.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="tab-panel classes-tab <?php echo e(($activeAdminTab ?? 'structure') === 'classes' ? 'active' : ''); ?>" data-panel="classes">
                <div class="card mb-16 classes-hero-card">
                    <div class="classes-hero-inner">
                        <div class="classes-hero-actions">
                            <span class="classes-hero-label">Actions</span>
                            <div class="class-actions">
                                <button type="button" class="btn sm primary" data-role-modal-open="class-modal-time-profile">Time Profile</button>
                                <button type="button" class="btn sm primary" data-role-modal-open="class-modal-group-builder">Group Builder</button>
                                <button type="button" class="btn sm primary" data-role-modal-open="class-modal-profiles-breaks">Profiles &amp; Breaks</button>
                                <button type="button" class="btn sm success" data-role-modal-open="class-modal-generate-schedule">Generate Schedule</button>
                                <form method="post" action="<?php echo e(url('/tenant/admin/classes/schedules/clear-all')); ?>" onsubmit="return confirm('Clear all generated schedules for all class groups? This cannot be undone.');" class="classes-hero-form-inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn sm danger" type="submit">Clear Schedules</button>
                                </form>
                            </div>
                        </div>
                        <div class="classes-hero-term">
                            <span class="classes-hero-label">Current term</span>
                            <form method="post" action="<?php echo e(url('/tenant/admin/classes/current-term')); ?>" class="classes-current-term-form">
                                <?php echo csrf_field(); ?>
                                <input id="classes-current-school-year" name="school_year" placeholder="2026-2027" value="<?php echo e(old('school_year', $currentSchoolYearName ?? '')); ?>" pattern="\d{4}\s*-\s*\d{4}" required class="classes-term-input">
                                <span class="classes-term-toggle">
                                    <label class="classes-term-option"><input type="radio" name="term_code" value="1ST" <?php echo e(old('term_code', $currentTermCode ?? '1ST') === '1ST' ? 'checked' : ''); ?>><span>1st</span></label>
                                    <label class="classes-term-option"><input type="radio" name="term_code" value="2ND" <?php echo e(old('term_code', $currentTermCode ?? '1ST') === '2ND' ? 'checked' : ''); ?>><span>2nd</span></label>
                                </span>
                                <button class="btn sm primary" type="submit">Save</button>
                            </form>
                        </div>
                        <div class="classes-stats">
                            <span class="classes-stat-pill"><?php echo e($classGroups->count()); ?> groups</span>
                            <span class="classes-stat-pill"><?php echo e($classDayProfiles->count()); ?> profiles</span>
                            <span class="classes-stat-pill"><?php echo e($teachers->count()); ?> teachers</span>
                            <?php if($selectedClassGroup): ?>
                            <span class="classes-stat-pill"><?php echo e($classSessions->count()); ?> sessions</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="classes-hero-cta-wrap mb-16">
                    <a href="<?php echo e(url('/tenant/admin/class-groups')); ?>" class="btn primary classes-hero-cta">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Manage Class Groups
                        <span class="classes-hero-cta-badge"><?php echo e($classGroupsCurrentTerm->count()); ?></span>
                    </a>
                    <p class="classes-hero-cta-hint">View and edit capacity and enrollment status for current semester groups.</p>
                </div>

                <div id="class-modal-time-profile" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Class Time Profile">
                        <div class="card-header">
                            <h2>Class Time Profile</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/classes/day-profiles')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Profile Name</label>
                                <input name="name" placeholder="Default Weekday Profile" required>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Class Starts</label>
                                    <input name="class_start_time" type="time" required>
                                </div>
                                <div class="form-group">
                                    <label>Class Ends</label>
                                    <input name="class_end_time" type="time" required>
                                </div>
                            </div>
                            <input type="hidden" name="slot_minutes" value="30">
                            <button class="btn primary" type="submit">Create Time Profile</button>
                        </form>
                    </div>
                </div>

                <div id="class-modal-group-builder" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Class Group Builder">
                        <div class="card-header">
                            <h2>Class Group Builder</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/classes/groups')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Program</label>
                                <select name="program_id" required>
                                    <?php $__currentLoopData = $programs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($p->id); ?>"><?php echo e($p->code); ?> - <?php echo e($p->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="inline">
                                <div class="form-group">
                                    <label>Semester</label>
                                    <select name="semester_id" required>
                                        <?php $__empty_4 = true; $__currentLoopData = $fixedSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixedSemester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?>
                                            <option value="<?php echo e($fixedSemester['model']->id); ?>"><?php echo e($fixedSemester['label']); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?>
                                            <option value="">No fixed semesters found</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Year Level</label>
                                    <select name="year_level" required>
                                        <?php for($y=1; $y<=8; $y++): ?>
                                            <option value="<?php echo e($y); ?>">Year <?php echo e($y); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Class Group Name</label>
                                <input name="name" placeholder="BSIT 1-A" required>
                            </div>
                            <div class="form-group">
                                <label>Day Profile</label>
                                <select name="day_profile_id" required>
                                    <?php $__empty_4 = true; $__currentLoopData = $classDayProfiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?>
                                        <option value="<?php echo e($profile->id); ?>"><?php echo e($profile->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?>
                                        <option value="">No profile yet - create one first</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Create Class Group</button>
                        </form>
                    </div>
                </div>

                <div id="class-modal-generate-schedule" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Generate Schedule">
                        <div class="card-header">
                            <h2>Generate Schedule</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <?php
                            $activeGenerationTermCode = strtoupper((string) ($currentTermCode ?? '1ST'));
                            $generatableSemesters = collect($fixedSemesters)->filter(function ($fixedSemester) use ($activeGenerationTermCode) {
                                return strtoupper((string) ($fixedSemester['model']->term_code ?? '')) === $activeGenerationTermCode;
                            })->values();
                        ?>
                        <form method="post" action="<?php echo e(url('/tenant/admin/classes/generate-semester')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Semester (Generate All Class Groups)</label>
                                <select name="semester_id" required>
                                    <?php $__empty_4 = true; $__currentLoopData = $generatableSemesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fixedSemester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?>
                                        <option value="<?php echo e($fixedSemester['model']->id); ?>"><?php echo e($fixedSemester['label']); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?>
                                        <option value="">No active semester available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Generate All (Semester)</button>
                        </form>
                        <?php if($selectedClassGroup): ?>
                            <div style="display:flex; gap:8px; margin-top:10px; flex-wrap:wrap;">
                                <form method="post" action="<?php echo e(url('/tenant/admin/classes/groups/' . $selectedClassGroup->id . '/draft-sessions/delete')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn danger sm" type="submit">Delete Draft Sessions</button>
                                </form>
                                <form method="post" action="<?php echo e(url('/tenant/admin/classes/groups/' . $selectedClassGroup->id . '/schedule/delete')); ?>" onsubmit="return confirm('Delete all scheduled sessions for this class group?');">
                                    <?php echo csrf_field(); ?>
                                    <button class="btn danger sm" type="submit">Delete Full Schedule</button>
                                </form>
                            </div>
                        <?php endif; ?>
                        <?php
                            $classGenerateReport = session('classes_generate_report');
                        ?>
                        <?php if(is_array($classGenerateReport) && ($classGenerateReport['scope'] ?? '') === 'semester'): ?>
                            <?php
                                $reportTotals = (array) ($classGenerateReport['totals'] ?? []);
                                $reportGroups = (array) ($classGenerateReport['groups'] ?? []);
                            ?>
                            <div class="class-run-summary">
                                <div><strong>Last Run:</strong> Semester-wide</div>
                                <div>Placed: <?php echo e((int) ($reportTotals['placed'] ?? 0)); ?></div>
                                <div>Unplaced: <?php echo e((int) ($reportTotals['unplaced_count'] ?? 0)); ?></div>
                                <div>TBA Teacher Sessions: <?php echo e((int) ($reportTotals['unassigned_teacher_count'] ?? 0)); ?></div>
                                <?php if($reportGroups !== []): ?>
                                    <div class="class-run-groups">
                                        <?php $__currentLoopData = $reportGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupReport): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="class-run-group-item">
                                                <strong><?php echo e($groupReport['class_group_name'] ?? ('Group #' . ($groupReport['class_group_id'] ?? '?'))); ?></strong>
                                                <span>Placed: <?php echo e((int) ($groupReport['placed'] ?? 0)); ?></span>
                                                <span>Unplaced: <?php echo e((int) ($groupReport['unplaced_count'] ?? 0)); ?></span>
                                                <span>TBA: <?php echo e((int) ($groupReport['unassigned_teacher_count'] ?? 0)); ?></span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php elseif($latestClassGenerationRun): ?>
                            <?php $runSummary = (array) ($latestClassGenerationRun->summary_json ?? []); ?>
                            <div class="class-run-summary">
                                <div><strong>Last Run:</strong> <?php echo e($latestClassGenerationRun->created_at); ?></div>
                                <div>Placed: <?php echo e((int) ($runSummary['placed'] ?? 0)); ?></div>
                                <div>Unplaced: <?php echo e((int) ($runSummary['unplaced_count'] ?? 0)); ?></div>
                                <div>TBA Teacher Sessions: <?php echo e((int) ($runSummary['unassigned_teacher_count'] ?? 0)); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="class-modal-profiles-breaks" class="role-form-modal class-profiles-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Profiles and Breaks">
                        <div class="card-header">
                            <h2>Profiles and Breaks</h2>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span class="badge blue"><?php echo e($classDayProfiles->count()); ?> profiles</span>
                                <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                            </div>
                        </div>
                        <div class="class-profile-grid">
                            <?php $__empty_4 = true; $__currentLoopData = $classDayProfiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?>
                                <div class="class-profile-card">
                                    <form method="post" action="<?php echo e(url('/tenant/admin/classes/day-profiles/' . $profile->id)); ?>" class="stack">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input name="name" value="<?php echo e($profile->name); ?>" required>
                                        </div>
                                        <div class="inline">
                                            <div class="form-group">
                                                <label>Start</label>
                                                <input name="class_start_time" type="time" value="<?php echo e(substr((string) $profile->class_start_time, 0, 5)); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>End</label>
                                                <input name="class_end_time" type="time" value="<?php echo e(substr((string) $profile->class_end_time, 0, 5)); ?>" required>
                                            </div>
                                        </div>
                                        <input type="hidden" name="slot_minutes" value="30">
                                        <div class="form-group">
                                            <label>Class Days</label>
                                            <div class="role-checkbox-list">
                                                <?php $__currentLoopData = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayValue => $dayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <label class="role-checkbox-item">
                                                        <input type="checkbox" name="days_mask[]" value="<?php echo e($dayValue); ?>" <?php echo e(in_array($dayValue, (array) ($profile->days_mask ?? []), true) ? 'checked' : ''); ?>>
                                                        <span><?php echo e($dayLabel); ?></span>
                                                    </label>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                        <button class="btn sm primary" type="submit">Update Profile</button>
                                    </form>

                                    <div class="class-break-list">
                                        <h4>Break Blocks</h4>
                                        <?php $__empty_5 = true; $__currentLoopData = $profile->breaks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $break): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_5 = false; ?>
                                            <div class="class-break-item">
                                                <span><?php echo e($break->label); ?> (<?php echo e(substr((string) $break->start_time, 0, 5)); ?>-<?php echo e(substr((string) $break->end_time, 0, 5)); ?>)</span>
                                                <form method="post" action="<?php echo e(url('/tenant/admin/classes/breaks/' . $break->id . '/remove')); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <button class="btn sm danger" type="submit">Remove</button>
                                                </form>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_5): ?>
                                            <div class="text-muted">No breaks yet.</div>
                                        <?php endif; ?>
                                    </div>

                                    <form method="post" action="<?php echo e(url('/tenant/admin/classes/day-profiles/' . $profile->id . '/breaks')); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group" style="flex:1;">
                                            <label>Label</label>
                                            <input name="label" placeholder="Lunch" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Start</label>
                                            <input name="start_time" type="time" required>
                                        </div>
                                        <div class="form-group">
                                            <label>End</label>
                                            <input name="end_time" type="time" required>
                                        </div>
                                        <div class="form-group" style="align-self:flex-end;">
                                            <button class="btn sm primary" type="submit">Add Break</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?>
                                <div class="text-muted">No class day profile yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="classes-hero-cta-wrap mb-16">
                    <a href="<?php echo e(url('/tenant/admin/classes/timetable')); ?>" class="btn primary classes-hero-cta">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M8 2v4"/><path d="M16 2v4"/><path d="M3 10h18"/>
                        </svg>
                        Weekly Timetable
                    </a>
                    <p class="classes-hero-cta-hint">View and edit the weekly schedule for a class group (sessions, teachers, move slots).</p>
                </div>
                <div class="classes-hero-cta-wrap mb-16">
                    <a href="<?php echo e(url('/tenant/admin/classes/teacher-schedule')); ?>" class="btn primary classes-hero-cta">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Teacher Weekly Schedule
                    </a>
                    <p class="classes-hero-cta-hint">View teaching load by teacher; filter by class group if needed.</p>
                </div>
            </div>

            <div class="tab-panel <?php echo e(($activeAdminTab ?? 'structure') === 'roles' ? 'active' : ''); ?>" data-panel="roles">
                <div class="card mb-20">
                    <div class="card-header">
                        <h2>School Staff Actions</h2>
                    </div>
                    <div class="role-actions">
                        <button type="button" class="btn primary" data-role-modal-open="role-modal-create-user">Create User Account</button>
                        <button type="button" class="btn primary" data-role-modal-open="role-modal-assign-role">Assign School Staff to User</button>
                        <button type="button" class="btn success" data-role-modal-open="role-modal-assign-teacher">Assign Teacher to Offering</button>
                    </div>
                </div>

                <div id="role-modal-create-user" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Create User Account">
                        <div class="card-header">
                            <h2>Create User Account</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/users')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Full Name</label>
                                <input name="full_name" placeholder="e.g. Jane Doe" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input name="email" type="email" placeholder="user@school.edu" required>
                            </div>
                            <div class="form-group">
                                <label>Phone (Optional)</label>
                                <input name="phone" placeholder="+63 9xx xxx xxxx">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input name="password" type="password" minlength="8" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input name="password_confirmation" type="password" minlength="8" required>
                            </div>
                            <div class="form-group">
                                <label>Assign Initial Role (Optional)</label>
                                <select name="role_id">
                                    <option value="">None</option>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?> (<?php echo e($r->code); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Create User</button>
                        </form>
                    </div>
                </div>

                <div id="role-modal-assign-role" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Assign School Staff to User">
                        <div class="card-header">
                            <h2>Assign School Staff to User</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/admin/roles/assign')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Select User</label>
                                <select name="user_id">
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($u->id); ?>"><?php echo e($u->full_name); ?> - <?php echo e($u->email); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Role</label>
                                <select name="role_id">
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($r->id); ?>"><?php echo e($r->name); ?> (<?php echo e($r->code); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button class="btn primary" type="submit">Assign School Staff</button>
                        </form>
                    </div>
                </div>

                <div id="role-modal-assign-teacher" class="role-form-modal" hidden>
                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Assign Teacher to Offering">
                        <div class="card-header">
                            <h2>Assign Teacher to Offering</h2>
                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                        </div>
                        <form method="post" action="<?php echo e(url('/tenant/dean/assign-teacher')); ?>" class="stack">
                            <?php echo csrf_field(); ?>
                            <div class="form-group">
                                <label>Subject Offering</label>
                                <select name="subject_offering_id">
                                    <?php $__currentLoopData = $offerings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($o->id); ?>">
                                            <?php echo e($o->subject->code ?? 'Subj #'.$o->subject_id); ?> - <?php echo e($o->semester->name ?? 'Sem #'.$o->semester_id); ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Teacher</label>
                                <select name="teacher_user_id">
                                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($t->id); ?>"><?php echo e($t->full_name); ?> - <?php echo e($t->email); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <button class="btn success" type="submit">Assign Teacher</button>
                        </form>
                    </div>
                </div>

                <div class="card user-management-card">
                    <div class="card-header">
                        <h2>User Management</h2>
                        <span class="badge blue"><?php echo e($managedUsers->count()); ?> users</span>
                    </div>
                    <div class="user-management-toolbar">
                        <div class="form-group" style="margin:0;">
                            <label>Search User</label>
                            <input id="user-management-search" type="search" placeholder="Search by name, email, school staff, or user id...">
                        </div>
                    </div>
                    <div class="um-role-legend" aria-label="Role legend">
                        <span class="um-role-legend-title">Roles</span>
                        <span class="um-role-legend-item"><span class="um-role-dot um-role-dot--school-admin" aria-hidden="true"></span> Admin</span>
                        <span class="um-role-legend-item"><span class="um-role-dot um-role-dot--teacher" aria-hidden="true"></span> Teacher</span>
                        <span class="um-role-legend-item"><span class="um-role-dot um-role-dot--dean" aria-hidden="true"></span> Dean</span>
                        <span class="um-role-legend-item"><span class="um-role-dot um-role-dot--registrar-staff" aria-hidden="true"></span> Registrar</span>
                        <span class="um-role-legend-item"><span class="um-role-dot um-role-dot--finance-staff" aria-hidden="true"></span> Finance</span>
                        <span class="um-role-legend-item"><span class="um-role-dot um-role-dot--default" aria-hidden="true"></span> Other</span>
                    </div>
                    <div class="um-cards-wrap">
                        <?php $__empty_4 = true; $__currentLoopData = $managedUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?>
                            <?php $u = $entry['user']; ?>
                            <?php
                                $hasActiveTeacherRole = collect($entry['roles'])->contains(function ($ur) {
                                    return ($ur->role->code ?? null) === 'teacher' && (bool) $ur->is_active;
                                });
                                $assignedTeacherSubjectIds = collect($teacherSubjectMap[$u->id] ?? [])->map(fn ($id) => (int) $id);
                            ?>
                            <div
                                class="um-card um-card--<?php echo e($u->status === 'active' ? 'active' : 'inactive'); ?>"
                                data-user-row
                                data-search="<?php echo e(strtolower(($u->full_name ?? '') . ' ' . ($u->email ?? '') . ' #' . ($u->id ?? '') . ' ' . ($u->phone ?? ''))); ?>"
                            >
                                <div class="um-card-banner"></div>
                                <div class="um-card-profile">
                                    <span class="um-card-avatar" aria-hidden="true"><?php echo e(strtoupper(substr($u->full_name ?? 'U', 0, 1))); ?></span>
                                    <div class="um-card-meta">
                                        <div class="um-name-row">
                                            <div class="um-user-name"><?php echo e($u->full_name); ?></div>
                                            <div class="um-name-dots" aria-label="Active school staffs">
                                                <?php $__currentLoopData = $entry['roles']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($ur->is_active): ?>
                                                        <?php $roleCode = str_replace('_', '-', $ur->role->code ?? 'default'); ?>
                                                        <span class="um-role-chip um-role-chip--<?php echo e($roleCode); ?>" title="<?php echo e(ucfirst(str_replace('_', ' ', $ur->role->code ?? 'role'))); ?>" aria-hidden="true"></span>
                                                    <?php endif; ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                        <span class="um-card-status um-card-status--<?php echo e($u->status === 'active' ? 'active' : 'inactive'); ?>" title="<?php echo e($u->status === 'active' ? 'Active' : 'Disabled'); ?>">
                                            <?php echo e($u->status === 'active' ? 'Active' : 'Disabled'); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="um-card-contact">
                                    <span class="um-contact-item">
                                        <svg class="um-contact-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <span><?php echo e($u->email); ?></span>
                                    </span>
                                    <span class="um-contact-item">
                                        <svg class="um-contact-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span><?php echo e($u->phone ?? 'No phone'); ?></span>
                                    </span>
                                </div>
                                <div class="um-card-roles">
                                    <div class="um-role-actions">
                                        <button type="button" class="btn sm ghost" data-role-modal-open="role-modal-user-<?php echo e($u->id); ?>">Edit staffs</button>
                                        <?php if($hasActiveTeacherRole): ?>
                                            <button type="button" class="btn sm primary" data-role-modal-open="teacher-subject-modal-user-<?php echo e($u->id); ?>">Teachable subjects</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="um-card-modals">
                                                <div id="role-modal-user-<?php echo e($u->id); ?>" class="role-form-modal" hidden>
                                                    <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Edit User School Staffs">
                                                        <div class="card-header">
                                                            <h2>Edit School Staffs: <?php echo e($u->full_name); ?></h2>
                                                            <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                                                        </div>

                                                        <div class="stack">
                                                            <div class="form-group">
                                                                <label>Current School Staffs</label>
                                                                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                                    <?php $__empty_6 = true; $__currentLoopData = $entry['roles']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_6 = false; ?>
                                                                        <form method="post" action="<?php echo e(url('/tenant/admin/user-roles/' . $ur->id . '/status')); ?>">
                                                                            <?php echo csrf_field(); ?>
                                                                            <input type="hidden" name="is_active" value="<?php echo e($ur->is_active ? 0 : 1); ?>">
                                                                            <button class="btn sm <?php echo e($ur->is_active ? 'ghost' : 'primary'); ?>" type="submit">
                                                                                <?php echo e($ur->is_active ? 'Deactivate' : 'Activate'); ?> <?php echo e($ur->role->name ?? $ur->role->code); ?>
                                                                            </button>
                                                                        </form>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_6): ?>
                                                                        <span class="text-muted">No school staffs assigned yet.</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <form method="post" action="<?php echo e(url('/tenant/admin/roles/assign-bulk')); ?>" class="stack">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="user_id" value="<?php echo e($u->id); ?>">
                                                                <div class="form-group">
                                                                    <label>Set Active School Staff(s)</label>
                                                                    <div class="role-checkbox-list">
                                                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                            <?php
                                                                                $isChecked = collect($entry['roles'])->contains(function ($ur) use ($r) {
                                                                                    return (int) $ur->role_id === (int) $r->id && (bool) $ur->is_active;
                                                                                });
                                                                            ?>
                                                                            <label class="role-checkbox-item">
                                                                                <input type="checkbox" name="role_ids[]" value="<?php echo e($r->id); ?>" <?php echo e($isChecked ? 'checked' : ''); ?>>
                                                                                <span><?php echo e($r->name); ?> (<?php echo e($r->code); ?>)</span>
                                                                            </label>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                    </div>
                                                                    <p class="text-muted">Check one or more school staffs to keep active, then save. Unchecked ones will be deactivated.</p>
                                                                </div>
                                                                <button class="btn primary" type="submit">Save School Staffs</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if($hasActiveTeacherRole): ?>
                                                    <div id="teacher-subject-modal-user-<?php echo e($u->id); ?>" class="role-form-modal" hidden>
                                                        <div class="role-form-dialog" role="dialog" aria-modal="true" aria-label="Set Teachable Subjects">
                                                            <div class="card-header">
                                                                <h2>Set Teachable Subjects: <?php echo e($u->full_name); ?></h2>
                                                                <button type="button" class="btn sm ghost" data-role-modal-close>Close</button>
                                                            </div>
                                                            <form method="post" action="<?php echo e(url('/tenant/admin/teachers/subjects/sync')); ?>" class="stack">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="user_id" value="<?php echo e($u->id); ?>">
                                                                <div class="form-group">
                                                                    <label>Allowed Subjects</label>
                                                                    <div class="role-checkbox-list" style="max-height: 280px; overflow: auto; border: 1px solid var(--border); border-radius: 10px; padding: 10px;">
                                                                        <?php $__empty_7 = true; $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_7 = false; ?>
                                                                            <label class="role-checkbox-item">
                                                                                <input
                                                                                    type="checkbox"
                                                                                    name="subject_ids[]"
                                                                                    value="<?php echo e($subject->id); ?>"
                                                                                    <?php echo e($assignedTeacherSubjectIds->contains((int) $subject->id) ? 'checked' : ''); ?>
                                                                                >
                                                                                <span><?php echo e($subject->code); ?> - <?php echo e($subject->title); ?></span>
                                                                            </label>
                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_7): ?>
                                                                            <span class="text-muted">No subjects available in Subject Bank yet.</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <p class="text-muted">Only checked subjects can be assigned to this teacher in offerings.</p>
                                                                </div>
                                                                <button class="btn primary" type="submit">Save Teachable Subjects</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?>
                            <div class="um-cards-empty">No school users found yet. Create a user and assign a school staff.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="tab-panel <?php echo e(($activeAdminTab ?? 'structure') === 'students' ? 'active' : ''); ?>" data-panel="students">
                <div class="card user-management-card">
                    <div class="card-header">
                        <h2>Students</h2>
                        <span class="badge blue"><?php echo e($studentUsers->count()); ?> students</span>
                    </div>
                    <div class="user-management-toolbar">
                        <div class="form-group" style="margin:0;">
                            <label>Search Student</label>
                            <input id="student-management-search" type="search" placeholder="Search by name, email, or user id...">
                        </div>
                    </div>
                    <div class="table-wrap">
                        <table class="user-management-table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Contact</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_4 = true; $__currentLoopData = $studentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_4 = false; ?>
                                    <?php $u = $entry['user']; ?>
                                    <tr
                                        data-student-row
                                        data-search="<?php echo e(strtolower(($u->full_name ?? '') . ' ' . ($u->email ?? '') . ' #' . ($u->id ?? '') . ' ' . ($u->phone ?? ''))); ?>"
                                    >
                                        <td class="um-user-cell">
                                            <div class="um-user-name"><?php echo e($u->full_name); ?></div>
                                            <div class="um-user-id">#<?php echo e($u->id); ?></div>
                                        </td>
                                        <td class="um-contact-cell">
                                            <div class="um-contact-primary"><?php echo e($u->email); ?></div>
                                            <div class="um-contact-secondary"><?php echo e($u->phone ?? 'No phone'); ?></div>
                                        </td>
                                        <td class="um-roles-cell">
                                            <span class="badge <?php echo e(($u->status ?? '') === 'active' ? 'blue' : 'amber'); ?>">
                                                student (<?php echo e(($u->status ?? '') === 'active' ? 'active' : 'inactive'); ?>)
                                            </span>
                                        </td>
                                        <td class="um-actions-cell">
                                            <form method="post" action="<?php echo e(url('/tenant/admin/users/' . $u->id . '/status')); ?>" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="status" value="<?php echo e($u->status === 'active' ? 'disabled' : 'active'); ?>">
                                                <button
                                                    class="btn sm um-icon-btn <?php echo e($u->status === 'active' ? 'danger' : 'success'); ?>"
                                                    type="submit"
                                                    title="<?php echo e($u->status === 'active' ? 'Disable student' : 'Enable student'); ?>"
                                                    aria-label="<?php echo e($u->status === 'active' ? 'Disable student' : 'Enable student'); ?>"
                                                >
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <circle cx="12" cy="12" r="9"></circle>
                                                        <line x1="7.5" y1="16.5" x2="16.5" y2="7.5"></line>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_4): ?>
                                    <tr>
                                        <td colspan="4" class="text-muted">No students found yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </main>
    </div>
</div>
<?php $__env->startPush('styles'); ?>
<style>
/* Add Department / Add Program — floating button */
.admin-structure-fab {
    position: fixed;
    right: 22px;
    bottom: 22px;
    z-index: 1400;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    border-radius: 999px;
    padding: 10px 14px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
}

.admin-structure-fab svg {
    flex: 0 0 auto;
}

.admin-structure-fab:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.35);
    outline-offset: 3px;
}

@media (max-width: 520px) {
    .admin-structure-fab {
        right: 16px;
        bottom: 16px;
        padding: 10px 12px;
        font-size: 0.9rem;
    }
}

/* Subject Bank — floating button on subjects tab */
.admin-subject-fab {
    position: fixed;
    right: 22px;
    bottom: 22px;
    z-index: 1400;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border-radius: 999px;
    padding: 10px 14px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.18);
}

.admin-subject-fab svg { flex: 0 0 auto; }

.admin-subject-fab:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.35);
    outline-offset: 3px;
}

@media (max-width: 520px) {
    .admin-subject-fab {
        right: 16px;
        bottom: 16px;
        padding: 10px 12px;
        font-size: 0.9rem;
    }
}

.admin-structure-panels {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.admin-structure-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.admin-structure-panel-title {
    margin: 0 0 16px;
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
}

.admin-structure-form-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 14px 18px;
}

.admin-structure-form-grid--program {
    gap: 14px 18px;
}

.admin-structure-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}

.admin-structure-field--grow { flex: 1 1 180px; }
.admin-structure-field--action {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: auto;
}

.admin-structure-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
}

.admin-structure-input {
    padding: 8px 12px;
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    min-width: 100px;
}

.admin-structure-input:focus {
    outline: none;
    border-color: #94a3b8;
    box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.2);
}

.admin-structure-field--grow .admin-structure-input { min-width: 140px; }

.admin-structure-hint {
    font-size: 0.8rem;
    color: #94a3b8;
}

@media (max-width: 900px) {
    .admin-structure-panels { grid-template-columns: 1fr; }
    .admin-structure-form-grid { align-items: stretch; }
    .admin-structure-field--action { margin-left: 0; margin-top: 4px; }
}

/* Academic Structure card — clean hierarchy */
.structure-card {
    padding: 0;
    overflow: hidden;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.structure-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
}
.structure-card-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #1e293b;
}
.structure-card-meta {
    display: flex;
    align-items: center;
    gap: 8px;
}
.structure-meta-pill {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #64748b;
}
.structure-meta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

body.program-modal-open {
    overflow: hidden;
}

.structure-tree {
    padding: 20px 24px 24px;
}

.structure-tree details {
    margin: 0;
}

.structure-tree summary {
    list-style: none;
    cursor: pointer;
}

.structure-tree summary::-webkit-details-marker {
    display: none;
}

.tree-school-summary {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
}

.tree-school-name {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1e293b;
    letter-spacing: -0.01em;
}

.tree-children {
    margin-top: 4px;
    margin-left: 20px;
    padding-left: 20px;
    border-left: 2px solid #e2e8f0;
}

.tree-department {
    margin-bottom: 16px;
}

.tree-department:last-child {
    margin-bottom: 0;
}

.tree-department-summary {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    margin: 4px 0;
    border-radius: 10px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}

.tree-department-summary:hover {
    background: #f1f5f9;
}

.tree-department-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #334155;
    flex: 1;
}

.tree-department-count {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}

.tree-program-list {
    margin-top: 8px;
    margin-left: 8px;
    padding-left: 16px;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.tree-program-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    width: 100%;
    text-align: left;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.9rem;
    transition: background 0.15s;
}

.tree-program-item:hover {
    background: #f8fafc;
}

.tree-program-item[aria-expanded="true"] {
    background: #f1f5f9;
    outline: 1px solid #e2e8f0;
}

.tree-program-code {
    font-weight: 600;
    color: #475569;
    font-size: 0.85rem;
    min-width: 52px;
}

.tree-program-name {
    flex: 1;
    color: #334155;
}

.tree-program-badge,
.tree-program-plan {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 2px 8px;
    border-radius: 6px;
    background: #f1f5f9;
    color: #64748b;
}

.tree-program-plan {
    background: #fef3c7;
    color: #92400e;
}

.program-plan-modal {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1200;
    padding: 20px;
}

.program-plan-modal[hidden] {
    display: none !important;
}

.program-plan-dialog {
    width: min(1200px, 96vw);
    max-height: 92vh;
    overflow: auto;
    background: #fcfdff;
    border: 1px solid #d7e3f6;
    border-radius: 14px;
    box-shadow: 0 24px 50px rgba(15, 23, 42, 0.24);
    padding: 14px;
}

.program-plan-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #dbe6f7;
}

.program-plan-title {
    margin-bottom: 0;
    font-size: 0.95rem;
}

.program-plan-list {
    margin-top: 10px;
}

.program-plan-draft {
    margin-top: 10px;
    border: 1px solid #dbe6f7;
    border-radius: 12px;
    background: #f8fbff;
    padding: 10px 12px;
}

.program-plan-draft-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.program-plan-draft-actions {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.program-plan-draft-badge {
    font-size: 0.75rem;
    letter-spacing: 0.02em;
    text-transform: uppercase;
}

.program-plan-draft-list {
    margin: 8px 0 0;
    padding: 0 0 0 18px;
    color: var(--ink-2);
}

.program-plan-draft-list li {
    margin: 2px 0;
}

.program-plan-row-pending-remove td {
    opacity: 0.65;
}

.program-plan-row-pending-remove td:nth-child(1),
.program-plan-row-pending-remove td:nth-child(2) {
    text-decoration: line-through;
}

.program-plan-remove[data-pending="true"] {
    background: #d97706;
}

.program-plan-remove[data-pending="true"]:hover {
    background: #b45309;
}

.year-segment {
    border: 1px solid #dbe6f7;
    border-radius: 12px;
    background: #ffffff;
    margin-top: 10px;
    overflow: hidden;
}

.year-segment-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    background: #f5f9ff;
    border-bottom: 1px solid #dbe6f7;
}

.year-segment-header h5 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--ink);
}

.semester-separator-row td {
    padding: 0;
    background: #f8fbff;
    border-bottom: 1px solid #dbe6f7;
}

.semester-separator {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 14px;
}

.semester-separator::before,
.semester-separator::after {
    content: '';
    height: 1px;
    background: #cfdcf4;
    flex: 1;
}

.semester-separator span {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--muted);
    white-space: nowrap;
}

.program-plan-subtitle {
    margin-bottom: 8px;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--muted);
}

.tree-program-name {
    font-weight: 500;
    color: var(--ink);
}

.tree-node-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    display: inline-block;
    flex-shrink: 0;
}

.tree-node-dot.school {
    background: #3b82f6;
}

.tree-node-dot.department {
    background: #10b981;
}

.tree-node-dot.program {
    background: #8b5cf6;
}

.subject-bank-scroll {
    max-height: 420px;
    overflow-y: auto;
}

.subject-row-toggle {
    cursor: pointer;
}

.subject-row-toggle:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.20);
    outline-offset: -3px;
}

.subject-bank-scroll table tbody tr.subject-row-toggle:hover {
    background: #f8fafc;
}

.role-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.class-profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 12px;
}

.classes-tab {
    position: relative;
}

.classes-tab .card {
    border-radius: 14px;
    border: 1px solid #dce6f6;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}

.classes-tab .card-header {
    background: linear-gradient(180deg, #fbfdff 0%, #f5f9ff 100%);
    border-bottom: 1px solid #dce6f6;
    padding-top: 12px;
    padding-bottom: 12px;
}

.classes-hero-card {
    background: var(--surface);
    border: 1px solid var(--border);
    padding: 12px 16px;
}

.classes-hero-inner {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px 24px;
}

.classes-hero-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--muted);
    flex-shrink: 0;
}

.classes-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.classes-hero-actions .class-actions { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }

.classes-hero-form-inline { margin: 0; display: inline; }

.classes-hero-term {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.classes-current-term-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.classes-term-input {
    width: 100px;
    padding: 6px 10px;
    font-size: 0.9rem;
    border: 1px solid var(--border);
    border-radius: var(--radius);
}

.classes-term-toggle {
    display: inline-flex;
    gap: 4px;
}

.classes-term-option {
    position: relative;
    display: inline-flex;
}

.classes-term-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.classes-term-option span {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    background: var(--surface);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
}

.classes-term-option input:checked + span {
    border-color: var(--accent);
    background: #eaf2ff;
    color: #1f4fbf;
}

.classes-stats {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-left: auto;
}

.classes-stat-pill {
    font-size: 0.8rem;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f1f5f9;
    color: var(--ink);
    font-weight: 600;
}

.classes-hero-cta-wrap {
    padding: 16px 20px;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    background: var(--surface);
}

.classes-hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.classes-hero-cta-badge {
    font-size: 0.8rem;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(255,255,255,0.25);
    font-weight: 600;
}

.classes-hero-cta-hint {
    margin: 10px 0 0;
    font-size: 0.875rem;
    color: var(--muted);
}

.class-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    align-items: center;
}

.class-actions form {
    margin: 0;
}

.class-filter-form {
    flex-wrap: nowrap;
    margin: 0;
    border: 1px solid #dce6f6;
    background: #f8fbff;
    border-radius: 12px;
    padding: 8px 10px;
}

.class-filter-form select {
    min-height: 36px;
    border-radius: 10px;
    border-color: #c8d7f2;
    background: #fff;
}

.class-filter-form label {
    font-weight: 600;
}

.class-profile-card {
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px;
    background: #fdfefe;
}

.class-break-list {
    border-top: 1px solid var(--border);
    margin-top: 10px;
    padding-top: 10px;
}

.class-break-list h4 {
    margin: 0 0 8px;
    font-size: 0.9rem;
}

.class-break-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 6px;
}

.class-run-summary {
    margin-top: 10px;
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px;
    background: #f8fbff;
    display: grid;
    gap: 4px;
    font-size: 0.9rem;
}

.class-run-groups {
    margin-top: 8px;
    border-top: 1px dashed #cfdcf0;
    padding-top: 8px;
    display: grid;
    gap: 6px;
}

.class-run-group-item {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 0.82rem;
}

.weekly-grid-wrap {
    border: 1px solid #d6e1f4;
    border-radius: 14px;
    margin-bottom: 12px;
    overflow: auto;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.weekly-grid {
    display: grid;
    grid-template-columns: 110px repeat(5, minmax(180px, 1fr));
    min-width: 980px;
}

.weekly-grid-head {
    font-weight: 700;
    font-size: 0.82rem;
    letter-spacing: .03em;
    text-transform: uppercase;
    color: var(--muted);
    background: #f7faff;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
    padding: 8px;
    position: sticky;
    top: 0;
    z-index: 4;
}

.weekly-grid-head.time-col {
    left: 0;
    z-index: 6;
}

.weekly-grid-time {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 8px;
    font-size: 0.82rem;
    color: var(--muted);
    font-weight: 600;
    background: #fcfdff;
    position: sticky;
    left: 0;
    z-index: 3;
}

.weekly-grid-time.hour {
    font-weight: 700;
    color: #3f5480;
    border-bottom-color: transparent;
}

.weekly-grid-time.half {
    color: transparent;
    border-bottom-style: solid;
    border-bottom-color: var(--border);
}

.weekly-grid-cell {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    min-height: 38px;
    height: 38px;
    padding: 0;
    position: relative;
    overflow: visible;
    background: rgba(255, 255, 255, 0.7);
}

.weekly-grid-cell.hour {
    border-bottom-color: transparent;
}

.weekly-grid-chip {
    border-radius: 8px;
    padding: 4px 6px;
    display: grid;
    gap: 2px;
    font-size: 0.74rem;
    line-height: 1.2;
}

.weekly-grid-chip-span {
    position: absolute;
    z-index: 5;
    box-sizing: border-box;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.10);
    border: 1px solid rgba(15, 23, 42, 0.08);
    backdrop-filter: blur(0.5px);
}

.weekly-grid-chip.lecture {
    background: #dcfce7;
    color: #166534;
}

.weekly-grid-chip.lab {
    background: #fef3c7;
    color: #92400e;
}

.class-session-actions {
    display: grid;
    gap: 6px;
}

.classes-tab .table-wrap {
    border: 1px solid #d6e1f4;
    border-radius: 12px;
    overflow: auto;
    background: #ffffff;
}

.classes-tab .class-session-table {
    margin: 0;
}

.classes-tab .class-session-table th {
    position: sticky;
    top: 0;
    z-index: 2;
    background: #f3f8ff;
}

.classes-tab .class-session-table tbody tr:nth-child(even) {
    background: #fbfdff;
}

.classes-tab .class-session-table tbody tr:hover {
    background: #eef5ff;
}

.class-session-table td {
    vertical-align: top;
}

.class-group-settings-card {
    padding: 0;
    overflow: hidden;
}

.class-group-settings-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: #fafbfc;
}

.class-group-settings-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
}

.class-group-settings-table-wrap {
    border: none;
    border-radius: 0;
    max-height: 60vh;
    overflow: auto;
}

.class-group-settings-card .class-group-settings-table-wrap {
    border: none;
}

.class-group-settings-table {
    width: 100%;
    margin: 0;
    font-size: 0.875rem;
}

.class-group-settings-table th {
    background: #f1f5f9;
    position: sticky;
    top: 0;
    z-index: 1;
    padding: 8px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: .03em;
    color: var(--muted);
}

.class-group-settings-table td {
    vertical-align: middle;
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
}

.class-group-settings-table tbody tr:nth-child(even) {
    background: #fafbfc;
}

.class-group-settings-table .class-group-name { font-weight: 600; white-space: nowrap; }
.class-group-settings-table .class-group-program { max-width: 220px; color: var(--muted); font-size: 0.82rem; }

.class-group-settings-cell {
    white-space: nowrap;
}

.class-group-settings-form {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.class-group-capacity-input {
    width: 64px;
    padding: 4px 8px;
    font-size: 0.85rem;
}

.class-group-enrollment-select {
    padding: 4px 8px;
    font-size: 0.85rem;
    min-width: 80px;
}

@media (max-width: 900px) {
    .classes-hero-inner {
        flex-direction: column;
        align-items: stretch;
    }

    .classes-stats { margin-left: 0; }

    .classes-hero-actions .class-actions {
        flex-wrap: wrap;
    }

    .class-filter-form {
        flex-wrap: wrap;
        width: 100%;
    }

    .class-group-settings-form {
        flex-wrap: wrap;
    }

    .class-group-capacity-input,
    .class-group-enrollment-select {
        min-width: 0;
    }
}

.user-management-toolbar {
    padding: 0 14px 12px;
}

/* User Management — mini profile cards */
.um-cards-wrap {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
    padding: 0 14px 14px;
}

@media (max-width: 1100px) {
    .um-cards-wrap {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 720px) {
    .um-cards-wrap {
        grid-template-columns: 1fr;
    }
}

.um-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.um-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
}

.um-card--inactive {
    opacity: 0.85;
}

.um-card--inactive .um-card-banner { background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%); }
.um-card--inactive .um-card-avatar { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }

.um-card-banner {
    height: 32px;
    background: linear-gradient(135deg,rgb(113, 116, 122) 0%,rgb(191, 197, 208) 100%);
}

.um-card-profile {
    position: relative;
    padding: 0 16px 14px;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 12px;
}

.um-card-avatar {
    width: 44px;
    height: 44px;
    margin-top: -22px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    border: 2px solid #fff;
}

.um-card-meta {
    flex: 1;
    min-width: 0;
    padding-top: 4px;
}

.um-name-row {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.um-name-row .um-user-name {
    margin-bottom: 0;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.um-name-dots {
    display: flex;
    align-items: center;
    margin-left: auto;
    padding-left: 6px;
}

.um-role-chip {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    border: 2px solid #fff;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.18);
    flex-shrink: 0;
}

.um-role-chip + .um-role-chip {
    margin-left: -5px;
}

.um-role-chip--school-admin,
.um-role-chip--admin { background: #2563eb; }

.um-role-chip--teacher { background: #dc2626; }

.um-role-chip--dean { background: #16a34a; }

.um-role-chip--registrar-staff,
.um-role-chip--registrar { background: #7c3aed; }

.um-role-chip--finance-staff,
.um-role-chip--finance { background: #d97706; }

.um-role-chip--default { background: #64748b; }

.um-user-name {
    font-weight: 700;
    font-size: 1.05rem;
    line-height: 1.3;
    color: #0f172a;
    margin-bottom: 2px;
}

.um-card .um-user-id {
    font-size: 0.75rem;
    color: #64748b;
    display: block;
    margin-bottom: 6px;
}

.um-card-status {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 2px 8px;
    border-radius: 6px;
}

.um-card-status--active {
    background: #dcfce7;
    color: #166534;
}

.um-card-status--inactive {
    background: #fef3c7;
    color: #92400e;
}

.um-card-contact {
    padding: 12px 16px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.um-contact-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.875rem;
    color: #475569;
}

.um-contact-icon {
    flex-shrink: 0;
    color: #94a3b8;
}

.um-card-roles {
    padding: 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-top: 1px solid #e2e8f0;
}

.um-card-roles .um-role-actions {
    margin-top: 0;
}

.um-roles-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 2px;
}

.um-role-legend {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 16px 20px;
    padding: 10px 14px 14px;
    font-size: 0.8rem;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.um-role-legend-title {
    font-weight: 600;
    color: #475569;
    margin-right: 4px;
}

.um-role-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.um-role-legend .um-role-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.um-role-dot--school-admin,
.um-role-dot--admin { background: #2563eb; }
.um-role-dot--teacher { background: #dc2626; }
.um-role-dot--dean { background: #16a34a; }
.um-role-dot--registrar-staff,
.um-role-dot--registrar { background: #7c3aed; }
.um-role-dot--finance-staff,
.um-role-dot--finance { background: #d97706; }
.um-role-dot--default { background: #64748b; }

.um-card-modals {
    height: 0;
}

.um-cards-empty {
    grid-column: 1 / -1;
    padding: 32px;
    text-align: center;
    color: var(--muted);
    background: #f8fafc;
    border-radius: 16px;
    border: 1px dashed #cbd5e1;
}

.user-management-table {
    width: 100%;
    table-layout: fixed;
}

.user-management-table th,
.user-management-table td {
    vertical-align: top;
}

.user-management-table th:nth-child(1),
.user-management-table td:nth-child(1) {
    width: 26%;
}

.user-management-table th:nth-child(2),
.user-management-table td:nth-child(2) {
    width: 20%;
}

.user-management-table th:nth-child(3),
.user-management-table td:nth-child(3) {
    width: 44%;
}

.user-management-table th:nth-child(4),
.user-management-table td:nth-child(4) {
    width: 10%;
}

.um-user-name {
    font-weight: 700;
    font-size: 1rem;
}

.um-user-id,
.um-contact-secondary {
    font-size: 0.78rem;
    color: var(--muted);
}

.um-contact-primary {
    font-weight: 500;
    font-size: 0.9rem;
}

.um-roles-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
}

.um-role-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    padding: 0;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    background: transparent;
    transition: transform 0.12s ease, box-shadow 0.12s ease;
}

.um-role-badge:hover {
    transform: scale(1.12);
}

.um-role-badge:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

.um-role-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.um-role-badge--school-admin .um-role-dot,
.um-role-badge--admin .um-role-dot { background: #2563eb; }
.um-role-badge--teacher .um-role-dot { background: #dc2626; }
.um-role-badge--dean .um-role-dot { background: #16a34a; }
.um-role-badge--registrar-staff .um-role-dot,
.um-role-badge--registrar .um-role-dot { background: #7c3aed; }
.um-role-badge--finance-staff .um-role-dot,
.um-role-badge--finance .um-role-dot { background: #d97706; }
.um-role-badge--default .um-role-dot { background: #64748b; }

.um-role-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.um-card .um-role-actions .btn {
    font-size: 0.8rem;
}

.um-actions-cell {
    text-align: center;
}

.um-actions-wrap {
    display: flex;
    justify-content: center;
}

.um-icon-btn {
    min-width: 42px;
    width: 42px;
    height: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.role-form-modal {
    position: fixed;
    inset: 0;
    z-index: 1300;
    background: rgba(15, 23, 42, 0.42);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}

.role-form-modal[hidden] {
    display: none !important;
}

.role-checkbox-list {
    display: grid;
    gap: 8px;
}

.role-checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
}

.role-checkbox-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
}

.role-form-dialog {
    width: min(700px, 100%);
    max-height: 90vh;
    overflow: auto;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: var(--shadow-lg);
    padding: 14px;
}

.class-profiles-modal .role-form-dialog {
    width: min(1200px, 98vw);
    max-height: 92vh;
}

@media (max-width: 900px) {
    .tree-school-name {
        font-size: 18px;
    }

    .tree-school-summary,
    .tree-department-summary {
        flex-wrap: wrap;
    }

    .program-plan-modal {
        padding: 10px;
    }

    .program-plan-dialog {
        width: 100%;
        max-height: 94vh;
        border-radius: 12px;
    }

    .user-management-table {
        min-width: 900px;
    }

    .class-session-table {
        min-width: 980px;
    }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.collapse-toggle').forEach((btn) => {
        const targetId = btn.getAttribute('data-collapse-target');
        const target = targetId ? document.getElementById(targetId) : null;
        if (!target) return;

        const syncLabel = () => {
            const expanded = !target.hasAttribute('hidden');
            btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            btn.textContent = expanded ? 'Collapse' : 'Expand';
        };

        btn.addEventListener('click', () => {
            if (target.hasAttribute('hidden')) {
                target.removeAttribute('hidden');
            } else {
                target.setAttribute('hidden', 'hidden');
            }
            syncLabel();
        });

        syncLabel();
    });

    const bindAutoSubmit = (formId, selectId) => {
        const form = document.getElementById(formId);
        const select = document.getElementById(selectId);
        if (!form || !select) {
            return;
        }

        let submitTimer = null;
        select.addEventListener('change', () => {
            if (submitTimer) {
                window.clearTimeout(submitTimer);
            }
            submitTimer = window.setTimeout(() => form.submit(), 80);
        });
    };

    const modalOpenKey = 'adminProgramPlanOpenModal';
    const removeUrlById = {};
    const draftStateById = {};
    const modalById = {};
    const toggleByModalId = {};

    const hasDraftChanges = (modalId) => {
        const state = draftStateById[modalId];
        if (!state) {
            return false;
        }

        return state.adds.length > 0 || Object.keys(state.removes).length > 0;
    };

    const formatPendingCount = (count) => `${count} pending ${count === 1 ? 'change' : 'changes'}`;

    const setDraftUI = (modalId) => {
        const modal = modalById[modalId];
        const state = draftStateById[modalId];
        if (!modal || !state) {
            return;
        }

        const draftBox = modal.querySelector('[data-plan-draft]');
        const draftCount = modal.querySelector('[data-draft-count]');
        const draftList = modal.querySelector('[data-draft-list]');
        if (!draftBox || !draftCount || !draftList) {
            return;
        }

        const removeIds = Object.keys(state.removes);
        const totalPending = state.adds.length + removeIds.length;

        draftBox.hidden = totalPending === 0;
        draftCount.textContent = formatPendingCount(totalPending);
        draftList.innerHTML = '';

        state.adds.forEach((item) => {
            const li = document.createElement('li');
            li.textContent = `Add: Year ${item.yearLevel} / ${item.semesterLabel} / ${item.subjectLabel}`;
            draftList.appendChild(li);
        });

        removeIds.forEach((itemId) => {
            const li = document.createElement('li');
            li.textContent = `Remove: ${state.removes[itemId]}`;
            draftList.appendChild(li);
        });

        modal.querySelectorAll('.program-plan-remove').forEach((btn) => {
            const itemId = btn.getAttribute('data-item-id');
            const isPending = !!state.removes[itemId];
            btn.dataset.pending = isPending ? 'true' : 'false';
            btn.textContent = isPending ? 'Undo' : 'Remove';

            const row = modal.querySelector(`tr[data-existing-item-id="${itemId}"]`);
            if (row) {
                row.classList.toggle('program-plan-row-pending-remove', isPending);
            }
        });
    };

    const resetDraftState = (modalId) => {
        if (!draftStateById[modalId]) {
            return;
        }

        draftStateById[modalId].adds = [];
        draftStateById[modalId].removes = {};
        setDraftUI(modalId);
    };

    const closeSingleModal = (modalId, options = {}) => {
        const modal = modalById[modalId];
        if (!modal) {
            return true;
        }

        const shouldConfirm = !options.force && hasDraftChanges(modalId);
        if (shouldConfirm) {
            const proceed = window.confirm('Discard unsaved curriculum changes?');
            if (!proceed) {
                return false;
            }
            resetDraftState(modalId);
        }

        modal.setAttribute('hidden', 'hidden');
        if (toggleByModalId[modalId]) {
            toggleByModalId[modalId].setAttribute('aria-expanded', 'false');
        }
        sessionStorage.removeItem(modalOpenKey);

        const hasVisibleModal = Object.values(modalById).some((panel) => !panel.hasAttribute('hidden'));
        if (!hasVisibleModal) {
            document.body.classList.remove('program-modal-open');
        }

        return true;
    };

    const closeProgramModals = (options = {}) => {
        const openModals = Object.values(modalById).filter((panel) => !panel.hasAttribute('hidden'));
        for (const modal of openModals) {
            if (!closeSingleModal(modal.id, options)) {
                return false;
            }
        }

        return true;
    };

    const openProgramModal = (modalId) => {
        const modal = modalById[modalId];
        if (!modal) {
            return;
        }

        modal.removeAttribute('hidden');
        if (toggleByModalId[modalId]) {
            toggleByModalId[modalId].setAttribute('aria-expanded', 'true');
        }
        document.body.classList.add('program-modal-open');
        sessionStorage.setItem(modalOpenKey, modalId);
    };

    document.querySelectorAll('.program-plan-modal').forEach((modal) => {
        const modalId = modal.id;
        modalById[modalId] = modal;
        draftStateById[modalId] = { adds: [], removes: {} };
        setDraftUI(modalId);

        modal.querySelectorAll('.program-plan-remove-form').forEach((form) => {
            const itemId = form.getAttribute('data-item-id');
            if (itemId) {
                removeUrlById[itemId] = form.getAttribute('action');
            }
        });

        const addForm = modal.querySelector('.program-plan-add-form');
        const addButton = modal.querySelector('.program-plan-add');
        const saveButton = modal.querySelector('.program-plan-save');
        const discardButton = modal.querySelector('.program-plan-discard');
        const programIdInput = addForm ? addForm.querySelector('input[name="program_id"]') : null;
        const tokenInput = addForm ? addForm.querySelector('input[name="_token"]') : null;
        const yearSelect = addForm ? addForm.querySelector('select[name="year_level"]') : null;
        const semesterSelect = addForm ? addForm.querySelector('select[name="semester_id"]') : null;
        const subjectSelect = addForm ? addForm.querySelector('select[name="subject_id"]') : null;

        if (addForm) {
            addForm.addEventListener('submit', (event) => {
                event.preventDefault();
            });
        }

        if (addButton && yearSelect && semesterSelect && subjectSelect) {
            addButton.addEventListener('click', () => {
                if (!yearSelect.value || !semesterSelect.value || !subjectSelect.value) {
                    return;
                }

                const state = draftStateById[modalId];
                const key = [yearSelect.value, semesterSelect.value, subjectSelect.value].join(':');
                const alreadyAdded = state.adds.some((item) => item.key === key);
                if (alreadyAdded) {
                    return;
                }

                const semesterLabel = semesterSelect.options[semesterSelect.selectedIndex]
                    ? semesterSelect.options[semesterSelect.selectedIndex].textContent.trim()
                    : `Semester ${semesterSelect.value}`;
                const semesterOrder = semesterSelect.options[semesterSelect.selectedIndex]
                    ? Number(semesterSelect.options[semesterSelect.selectedIndex].dataset.termOrder || 99)
                    : 99;
                const subjectLabel = subjectSelect.options[subjectSelect.selectedIndex]
                    ? subjectSelect.options[subjectSelect.selectedIndex].textContent.trim()
                    : `Subject ${subjectSelect.value}`;

                state.adds.push({
                    key,
                    yearLevel: yearSelect.value,
                    semesterId: semesterSelect.value,
                    semesterLabel,
                    semesterOrder,
                    subjectId: subjectSelect.value,
                    subjectLabel,
                });
                setDraftUI(modalId);
            });
        }

        modal.querySelectorAll('.program-plan-remove').forEach((btn) => {
            btn.addEventListener('click', () => {
                const itemId = btn.getAttribute('data-item-id');
                if (!itemId) {
                    return;
                }

                const state = draftStateById[modalId];
                if (state.removes[itemId]) {
                    delete state.removes[itemId];
                } else {
                    const row = modal.querySelector(`tr[data-existing-item-id="${itemId}"]`);
                    const subjectLabel = row && row.children[1] ? row.children[1].textContent.trim() : `Item ${itemId}`;
                    state.removes[itemId] = subjectLabel;
                }
                setDraftUI(modalId);
            });
        });

        if (discardButton) {
            discardButton.addEventListener('click', () => {
                resetDraftState(modalId);
            });
        }

        if (saveButton && addForm && programIdInput && tokenInput) {
            saveButton.addEventListener('click', async () => {
                const state = draftStateById[modalId];
                if (!state || (!state.adds.length && !Object.keys(state.removes).length)) {
                    return;
                }

                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';

                const sendForm = async (url, payload) => {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: new URLSearchParams(payload),
                    });

                    if (!response.ok) {
                        let message = `Request failed (${response.status}).`;
                        const contentType = response.headers.get('content-type') || '';
                        if (contentType.includes('application/json')) {
                            const data = await response.json();
                            const firstError = data.errors
                                ? Object.values(data.errors).flat()[0]
                                : null;
                            if (typeof firstError === 'string' && firstError.length > 0) {
                                message = firstError;
                            } else if (typeof data.message === 'string' && data.message.length > 0) {
                                message = data.message;
                            }
                        }
                        throw new Error(message);
                    }
                };

                try {
                    const csrf = tokenInput.value;
                    const removeIds = Object.keys(state.removes);
                    for (const itemId of removeIds) {
                        const removeUrl = removeUrlById[itemId];
                        if (!removeUrl) {
                            continue;
                        }

                        await sendForm(removeUrl, { _token: csrf });
                    }

                    const orderedAdds = [...state.adds].sort((a, b) => {
                        const yearDiff = Number(a.yearLevel) - Number(b.yearLevel);
                        if (yearDiff !== 0) {
                            return yearDiff;
                        }

                        return Number(a.semesterOrder || 99) - Number(b.semesterOrder || 99);
                    });

                    for (const item of orderedAdds) {
                        await sendForm(addForm.getAttribute('action'), {
                            _token: csrf,
                            program_id: programIdInput.value,
                            year_level: item.yearLevel,
                            semester_id: item.semesterId,
                            subject_id: item.subjectId,
                        });
                    }

                    sessionStorage.setItem(modalOpenKey, modalId);
                    window.location.reload();
                } catch (error) {
                    console.error(error);
                    alert(error && error.message ? error.message : 'Failed to save curriculum changes. Please try again.');
                    saveButton.disabled = false;
                    saveButton.textContent = 'Save Changes';
                }
            });
        }
    });

    closeProgramModals({ force: true });

    document.querySelectorAll('.program-plan-toggle').forEach((btn) => {
        const targetId = btn.getAttribute('data-target');
        const target = targetId ? document.getElementById(targetId) : null;
        if (!targetId || !target) {
            return;
        }

        toggleByModalId[targetId] = btn;

        btn.addEventListener('click', () => {
            const isOpen = !target.hasAttribute('hidden');
            if (isOpen) {
                closeSingleModal(targetId);
                return;
            }

            if (!closeProgramModals()) {
                return;
            }

            openProgramModal(targetId);
        });
    });

    document.querySelectorAll('.program-plan-close').forEach((btn) => {
        const targetId = btn.getAttribute('data-close-target');
        if (!targetId) {
            return;
        }

        btn.addEventListener('click', () => {
            closeSingleModal(targetId);
        });
    });

    document.querySelectorAll('.program-plan-modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeSingleModal(modal.id);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeProgramModals();
        }
    });

    const reopenModalId = sessionStorage.getItem(modalOpenKey);
    if (reopenModalId && modalById[reopenModalId]) {
        sessionStorage.removeItem(modalOpenKey);
        openProgramModal(reopenModalId);
    }

    const dept = document.getElementById('planner_department_id');
    const program = document.getElementById('planner_program_id');
    if (dept && program) {
        const options = Array.from(program.options).map(opt => ({
            value: opt.value,
            text: opt.text,
            dept: opt.dataset.departmentId || '',
        }));

        const rebuild = () => {
            const selectedDept = dept.value;
            const currentValue = program.value;
            program.innerHTML = '';

            options.forEach(opt => {
                if (selectedDept !== '' && opt.dept !== selectedDept) return;
                const el = document.createElement('option');
                el.value = opt.value;
                el.textContent = opt.text;
                el.dataset.departmentId = opt.dept;
                program.appendChild(el);
            });

            if (Array.from(program.options).some(o => o.value === currentValue)) {
                program.value = currentValue;
            }
        };

        dept.addEventListener('change', rebuild);
        rebuild();
    }

    let openRoleModal = null;
    const showRoleModal = (modal) => {
        if (!modal) {
            return;
        }
        modal.removeAttribute('hidden');
        document.body.classList.add('program-modal-open');
        openRoleModal = modal;
    };
    const hideRoleModal = (modal) => {
        if (!modal) {
            return;
        }
        modal.setAttribute('hidden', 'hidden');
        if (openRoleModal === modal) {
            openRoleModal = null;
        }
        if (!openRoleModal) {
            document.body.classList.remove('program-modal-open');
        }
    };

    document.querySelectorAll('[data-role-modal-open]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-role-modal-open');
            const modal = targetId ? document.getElementById(targetId) : null;
            if (!modal) {
                return;
            }
            if (openRoleModal && openRoleModal !== modal) {
                hideRoleModal(openRoleModal);
            }
            showRoleModal(modal);
        });
    });

    document.querySelectorAll('.role-form-modal').forEach((modal) => {
        modal.querySelectorAll('[data-role-modal-close]').forEach((closeBtn) => {
            closeBtn.addEventListener('click', () => hideRoleModal(modal));
        });
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                hideRoleModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && openRoleModal) {
            hideRoleModal(openRoleModal);
        }
    });

    const userSearchInput = document.getElementById('user-management-search');
    if (userSearchInput) {
        const rows = Array.from(document.querySelectorAll('[data-user-row]'));
        const filterRows = () => {
            const query = (userSearchInput.value || '').trim().toLowerCase();
            rows.forEach((row) => {
                const haystack = (row.getAttribute('data-search') || '').toLowerCase();
                row.hidden = query !== '' && !haystack.includes(query);
            });
        };

        userSearchInput.addEventListener('input', filterRows);
        filterRows();
    }

    const studentSearchInput = document.getElementById('student-management-search');
    if (studentSearchInput) {
        const rows = Array.from(document.querySelectorAll('[data-student-row]'));
        const filterRows = () => {
            const query = (studentSearchInput.value || '').trim().toLowerCase();
            rows.forEach((row) => {
                const haystack = (row.getAttribute('data-search') || '').toLowerCase();
                row.hidden = query !== '' && !haystack.includes(query);
            });
        };

        studentSearchInput.addEventListener('input', filterRows);
        filterRows();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\elearn\resources\views\tenant\admin.blade.php ENDPATH**/ ?>

