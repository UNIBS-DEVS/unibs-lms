<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-header p-3 border-bottom text-center">
        <img src="{{ asset('assets/images/company-logo.png') }}" class="sidebar-logo mb-2">
    </div>

    <!-- Dashboard (ALL ROLES) -->
    <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <i class="fa fa-gauge"></i>
        <span>Dashboard</span>
    </a>

    {{-- ================= ADMIN ONLY ================= --}}
    @if (auth()->user()->role === 'admin')
        <a data-bs-toggle="collapse" href="#adminMenu" role="button"
            aria-expanded="{{ request()->routeIs('users.*', 'courses.*', 'batches.*', 'batch-toc.*', 'batch-learners.*') ? 'true' : 'false' }}"
            aria-controls="adminMenu">

            <div class="d-flex align-items-center">
                <i class="fa fa-user-shield"></i>
                <span>Administration</span>
            </div>

            <i class="fa fa-chevron-down ms-auto"></i>
        </a>

        <div class="collapse {{ request()->routeIs('users.*', 'courses.*', 'batches.*', 'batch-toc.*', 'batch-learners.*') ? 'show' : '' }}"
            id="adminMenu">

            <!-- Users -->
            <a href="{{ route('users.index') }}"
                class="sidebar-submenu {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa fa-users"></i>
                <span>Users</span>
            </a>

            <!-- Courses -->
            <a href="{{ route('courses.index') }}"
                class="sidebar-submenu {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                <i class="fa fa-book"></i>
                <span>Courses</span>
            </a>

            <!-- Batch Management -->
            <a data-bs-toggle="collapse" href="#batchMenu" role="button" class="sidebar-submenu"
                aria-expanded="{{ request()->routeIs('batches.*', 'batch-toc.*', 'batch-learners.*') ? 'true' : 'false' }}"
                aria-controls="batchMenu">

                <div class="d-flex align-items-center">
                    <i class="fa fa-layer-group"></i>
                    <span>Batch Management</span>
                </div>

                <i class="fa fa-chevron-down ms-auto"></i>
            </a>

            <div class="collapse {{ request()->routeIs('batches.*', 'batches.toc.*', 'batch-learners.*', 'batch-courses.*', 'batch-trainers.*') ? 'show' : '' }}"
                id="batchMenu">

                <a href="{{ route('batches.index') }}"
                    class="sidebar-submenu {{ request()->routeIs('batches.*') ? 'active' : '' }}"
                    style="padding-left:75px">
                    <i class="fa fa-layer-group"></i>
                    <span>Batches</span>
                </a>

                <a href="{{ route('batch-courses.index') }}"
                    class="sidebar-submenu {{ request()->routeIs('batch-courses.*') ? 'active' : '' }}"
                    style="padding-left:75px">
                    <i class="fa fa-book-open"></i>
                    <span>Courses</span>
                </a>

                <a href="{{ route('batch-trainers.index') }}"
                    class="sidebar-submenu {{ request()->routeIs('batch-trainers.*') ? 'active' : '' }}"
                    style="padding-left:75px">
                    <i class="fa fa-chalkboard-user"></i>
                    <span>Trainers</span>
                </a>

                <a href="{{ route('batch-learners.index') }}"
                    class="sidebar-submenu {{ request()->routeIs('batch-learners.*') ? 'active' : '' }}"
                    style="padding-left:75px">
                    <i class="fa fa-user-graduate"></i>
                    <span>Learners</span>
                </a>

            </div>
        </div>
    @endif
    {{-- ================= END ADMIN ================= --}}

    {{-- ================= ADMIN + TRAINER ================= --}}
    @if (in_array(auth()->user()->role, ['admin', 'trainer']))
        <!-- Batch Tracing Dropdown -->
        <a data-bs-toggle="collapse" href="#batchTracingMenu" role="button" class="sidebar-submenu"
            aria-expanded="{{ request()->routeIs('batch-sessions.*', 'study-material.*') ? 'true' : 'false' }}"
            aria-controls="batchTracingMenu">

            <div class="d-flex align-items-center">
                <i class="fa fa-chart-line"></i>
                <span>Batch Tracking</span>
            </div>

            <i class="fa fa-chevron-down ms-auto"></i>
        </a>

        <div class="collapse {{ request()->routeIs('batch-sessions.*', 'study-material.*') ? 'show' : '' }}"
            id="batchTracingMenu">

            <a href="{{ route('batch-sessions.index') }}"
                class="sidebar-submenu {{ request()->routeIs('batch-sessions.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-calendar-days"></i>
                <span>Sessions</span>
            </a>

            <a href="{{ route('progress.index') }}"
                class="sidebar-submenu {{ request()->routeIs('progress.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-chart-line"></i>
                <span>Progress</span>
            </a>

        </div>
    @endif
    {{-- ================= END ADMIN + TRAINER ================= --}}

    {{-- ================= QUIZZES ================= --}}
    <a data-bs-toggle="collapse" href="#quizMenu" role="button" class="sidebar-submenu"
        aria-expanded="{{ request()->routeIs('quizzes.*') ? 'true' : 'false' }}" aria-controls="quizMenu">

        <div class="d-flex align-items-center">
            <i class="fa fa-question-circle"></i>
            <span>Quizzes</span>
        </div>

        <i class="fa fa-chevron-down ms-auto"></i>
    </a>

    <div class="collapse {{ request()->routeIs('quizzes.*') ? 'show' : '' }}" id="quizMenu">

        {{-- ADMIN: Manage + Attempt --}}
        @if (auth()->user()->role === 'admin')
            <a href="{{ route('quizzes.index') }}"
                class="sidebar-submenu {{ request()->routeIs('quizzes.index') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-cogs"></i>
                <span>Manage</span>
            </a>
        @endif

        @if (in_array(auth()->user()->role, ['admin', 'learner']))
            <a href="{{ route('quiz.attempt.index') }}"
                class="sidebar-submenu {{ request()->routeIs('quiz.attempt.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-play-circle"></i>
                <span>Attempt</span>
            </a>
        @endif

        {{-- TRAINER: Review quizzes --}}
        @if (in_array(auth()->user()->role, ['admin', 'trainer']))
            <a href="{{ route('trainer.quiz-reviews.index') }}"
                class="sidebar-submenu {{ request()->routeIs('trainer.quiz-reviews.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-check-circle"></i>
                <span>Review Quizzes</span>
            </a>
        @endif


    </div>
    {{-- ================= END QUIZZES ================= --}}

    {{-- ================= FEEDBACK ================= --}}
    <a data-bs-toggle="collapse" href="#feedbackMenu" role="button" class="sidebar-submenu"
        aria-expanded="{{ request()->routeIs('feedback.*') ? 'true' : 'false' }}" aria-controls="feedbackMenu">

        <div class="d-flex align-items-center">
            <i class="fa fa-comment-dots"></i>
            <span>Feedbacks</span>
        </div>

        <i class="fa fa-chevron-down ms-auto"></i>
    </a>

    <div class="collapse {{ request()->routeIs('feedback.*') ? 'show' : '' }}" id="feedbackMenu">

        {{-- Default Feedback Question Management --}}
        @if (in_array(auth()->user()->role, ['admin', 'trainer']))
            <a href="{{ route('feedback.trainer.index') }}"
                class="sidebar-submenu {{ request()->routeIs('') ? 'active' : '' }}" style="padding-left:75px">
                <i class="fa fa-comments"></i>
                <span>Management</span>
            </a>
        @endif

        {{-- Trainer, Learner Feedbacks --}}
        @if (in_array(auth()->user()->role, ['admin', 'trainer', 'learner']))
            <a href="{{ route('feedback.share.index') }}"
                class="sidebar-submenu {{ request()->routeIs('feedback.share.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-paper-plane"></i>
                <span>Share Feedback</span>
            </a>
        @endif

    </div>
    {{-- ================= END FEEDBACK ================= --}}

    {{-- ================= STUDY MATERIAL ================= --}}
    @if (in_array(auth()->user()->role, ['admin', 'trainer']))
        <a data-bs-toggle="collapse" href="#studyMaterialMenu" role="button" class="sidebar-submenu"
            aria-expanded="{{ request()->routeIs('study-material.*') ? 'true' : 'false' }}"
            aria-controls="studyMaterialMenu">
            <div class="d-flex align-items-center">
                <i class="fa fa-book"></i>
                <span>Study Material</span>
            </div>
            <i class="fa fa-chevron-down ms-auto"></i>
        </a>

        <div class="collapse {{ request()->routeIs('study-material.*') ? 'show' : '' }}" id="studyMaterialMenu">
            <a href="#"
                class="sidebar-submenu {{ request()->routeIs('study-material.course-content') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-file-alt"></i>
                <span>Course Content</span>
            </a>

            <a href="{{ route('questions.index') }}"
                class="sidebar-submenu {{ request()->routeIs('study-material.question-bank') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-question-circle"></i>
                <span>Question Bank</span>
            </a>
        </div>
    @endif
    {{-- ================= END STUDY MATERIAL ================= --}}

    {{-- ================= REPORTS ================= --}}
    @if (in_array(auth()->user()->role, ['admin', 'trainer', 'learner']))
        <a data-bs-toggle="collapse" href="#reportsMenu" role="button" class="sidebar-submenu"
            aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}" aria-controls="reportsMenu">

            <div class="d-flex align-items-center">
                <i class="fa fa-chart-pie"></i>
                <span>Reports</span>
            </div>

            <i class="fa fa-chevron-down ms-auto"></i>
        </a>

        <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reportsMenu">

            <a href="{{ route('reports.attendance.index') }}"
                class="sidebar-submenu {{ request()->routeIs('reports.attendance.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-calendar-check"></i>
                <span>Attendance</span>
            </a>

            <a href="{{ route('reports.quiz.index') }}"
                class="sidebar-submenu {{ request()->routeIs('reports.quiz.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-question-circle"></i>
                <span>Quiz</span>
            </a>

            <a href="{{ route('reports.feedback.index') }}"
                class="sidebar-submenu {{ request()->routeIs('reports.feedback.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-comment"></i>
                <span>Feedback</span>
            </a>

            <a href="{{ route('reports.performance.index') }}"
                class="sidebar-submenu {{ request()->routeIs('reports.performance.*') ? 'active' : '' }}"
                style="padding-left:75px">
                <i class="fa fa-chart-line"></i>
                <span>Performance</span>
            </a>

        </div>
    @endif
    {{-- ================= END REPORTS ================= --}}


</aside>
