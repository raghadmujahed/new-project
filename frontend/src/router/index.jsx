import { BrowserRouter, Routes, Route } from "react-router-dom";
import MainLayout from "../app/layouts/MainLayout";
import ProtectedRoute from "./ProtectedRoute";

// Auth
import Login from "../pages/auth/Login";

// Common
import Profile from "../pages/common/Profile";
import ChangePassword from "../pages/common/ChangePassword";
import Notifications from "../pages/common/Notifications";

// Dashboards
import AdminDashboard from "../pages/dashboard/AdminDashboard";
import StudentDashboard from "../pages/dashboard/StudentDashboard";
import SupervisorDashboard from "../pages/dashboard/SupervisorDashboard";
import MentorDashboard from "../pages/dashboard/MentorDashboard";
import CoordinatorDashboard from "../pages/dashboard/CoordinatorDashboard";
import PrincipalDashboard from "../pages/dashboard/PrincipalDashboard";
import HealthDirectorateDashboard from "../pages/dashboard/HealthDirectorateDashboard";
import EducationDirectorateDashboard from "../pages/dashboard/EducationDirectorateDashboard";

// Admin: Users
import UsersList from "../pages/Admin/Users/UsersList";
import AddStudent from "../pages/Admin/Users/AddStudent";
import AddTeacher from "../pages/Admin/Users/AddTeacher";
import AddCounselor from "../pages/Admin/Users/AddCounselor";
import AddPsychologist from "../pages/Admin/Users/AddPsychologist";
import AddAcademicSupervisor from "../pages/Admin/Users/AddAcademicSupervisor";
import AddSchoolManager from "../pages/Admin/Users/AddSchoolManager";

// Admin: Roles & Permissions
import RolesList from "../pages/admin/Roles/RolesList";
import RoleForm from "../pages/admin/Roles/RoleForm";
import PermissionsList from "../pages/admin/Permissions/PermissionsList";

// Admin: Departments
import DepartmentsList from "../pages/admin/Departments/DepartmentsList";
import DepartmentForm from "../pages/admin/Departments/DepartmentForm";

// Admin: Courses
import CoursesList from "../pages/admin/Courses/CoursesList";
import CourseForm from "../pages/admin/Courses/CourseForm";

// Admin: Sections
import SectionsList from "../pages/Admin/Sections/SectionsList";
import SectionForm from "../pages/Admin/Sections/SectionForm";
import BulkUploadSections from "../pages/Admin/Sections/BulkUploadSections";
import AddStudentsToSection from "../pages/Admin/Sections/AddStudentsToSection";

// Admin: Enrollments
import EnrollmentsList from "../pages/Admin/Enrollments/EnrollmentsList";
import EnrollmentForm from "../pages/Admin/Enrollments/EnrollmentForm";

// Admin: Training Sites & Periods
import TrainingSitesList from "../pages/admin/TrainingSites/TrainingSitesList";
import TrainingSiteForm from "../pages/admin/TrainingSites/TrainingSiteForm";
import TrainingPeriodsList from "../pages/admin/TrainingPeriods/TrainingPeriodsList";
import TrainingPeriodForm from "../pages/admin/TrainingPeriods/TrainingPeriodForm";

// Admin: Announcements
import AnnouncementsList from "../pages/admin/Announcements/AnnouncementsList";
import AnnouncementForm from "../pages/admin/Announcements/AnnouncementForm";

// Admin: System
import BackupsList from "../pages/admin/Backups/BackupsList";
import BackupDetails from "../pages/Admin/Backups/BackupDetails";
import ActivityLogsList from "../pages/admin/ActivityLogs/ActivityLogsList";
import FeatureFlagsList from "../pages/admin/FeatureFlags/FeatureFlagsList";

import ImportSections from "../pages/Admin/Sections/ImportSections";
import BulkAddStudents from "../pages/Admin/Sections/BulkAddStudents";

// Admin: Evaluation
import EvaluationTemplatesList from "../pages/admin/EvaluationTemplates/EvaluationTemplatesList";
import EvaluationTemplateForm from "../pages/admin/EvaluationTemplates/EvaluationTemplateForm";

// Reports
import ReportsDashboard from "../pages/reports/ReportsDashboard";

// Student Portal
import Schedule from "../pages/student/Schedule";
import Portfolio from "../pages/student/Portfolio";
import TrainingLog from "../pages/student/TrainingLog";
import Assignments from "../pages/student/Assignments";
import NotificationsUpdates from "../pages/student/NotificationsUpdates";
import TrainingRequest from "../pages/Student/TrainingRequest";

// Supervisor Portal
import Tasks from "../pages/supervisor/Tasks";
import FieldVisits from "../pages/supervisor/FieldVisits";
import Sections from "../pages/supervisor/Sections";
import Evaluations from "../pages/supervisor/Evaluations";
import SupervisorReports from "../pages/supervisor/Reports";
import Submissions from "../pages/supervisor/Submissions";

// Mentor Portal
import Attendance from "../pages/mentor/Attendance";

// Principal Portal
import PrincipalProfile from "../pages/principal/Profile";
import MentorAssignment from "../pages/principal/MentorAssignment";
import TraineeStudents from "../pages/principal/TraineeStudents";
import PrincipalOfficialLetters from "../pages/principal/OfficialLetters";

// Education Directorate
import EducationTrainingSites from "../pages/educationDirectorate/TrainingSites";
import EducationOfficialLetters from "../pages/educationDirectorate/OfficialLetters";
import TableData from "../pages/Admin/Backups/TableData";


export default function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Login />} />

        <Route
          element={
            <ProtectedRoute>
              <MainLayout />
            </ProtectedRoute>
          }
        >
          {/* Dashboard */}
          <Route path="/dashboard" element={<AdminDashboard />} />


// داخل Routes
<Route path="/admin/backups/:id/table/:tableName" element={<TableData />} />

          {/* Users Management */}
          <Route path="/admin/users" element={<UsersList />} />
          <Route path="/admin/users/add/student" element={<AddStudent />} />
          <Route path="/admin/users/edit/student/:id" element={<AddStudent />} />
          <Route path="/admin/users/add/teacher" element={<AddTeacher />} />
          <Route path="/admin/users/edit/teacher/:id" element={<AddTeacher />} />
          <Route path="/admin/users/add/counselor" element={<AddCounselor />} />
          <Route path="/admin/users/edit/counselor/:id" element={<AddCounselor />} />
          <Route path="/admin/users/add/psychologist" element={<AddPsychologist />} />
          <Route path="/admin/users/edit/psychologist/:id" element={<AddPsychologist />} />
          <Route path="/admin/users/add/academic-supervisor" element={<AddAcademicSupervisor />} />
          <Route path="/admin/users/edit/academic-supervisor/:id" element={<AddAcademicSupervisor />} />
          <Route path="/admin/users/add/schoolmanager" element={<AddSchoolManager />} />
          <Route path="/admin/users/edit/schoolmanager/:id" element={<AddSchoolManager />} />

          {/* Roles & Permissions */}
          <Route path="/admin/roles" element={<RolesList />} />
          <Route path="/admin/roles/create" element={<RoleForm />} />
          <Route path="/admin/roles/edit/:id" element={<RoleForm />} />
          <Route path="/admin/permissions" element={<PermissionsList />} />

          {/* Departments */}
          <Route path="/admin/departments" element={<DepartmentsList />} />
          <Route path="/admin/departments/create" element={<DepartmentForm />} />
          <Route path="/admin/departments/edit/:id" element={<DepartmentForm />} />

          {/* Courses */}
          <Route path="/admin/courses" element={<CoursesList />} />
          <Route path="/admin/courses/create" element={<CourseForm />} />
          <Route path="/admin/courses/edit/:id" element={<CourseForm />} />

          {/* Sections */}
          <Route path="/admin/sections" element={<SectionsList />} />
          <Route path="/admin/sections/create" element={<SectionForm />} />
          <Route path="/admin/sections/edit/:id" element={<SectionForm />} />
          <Route path="/admin/sections/bulk-upload" element={<BulkUploadSections />} />
          <Route path="/admin/sections/:id/add-students" element={<AddStudentsToSection />} />

          {/* Enrollments */}
          <Route path="/admin/enrollments" element={<EnrollmentsList />} />
          <Route path="/admin/enrollments/create" element={<EnrollmentForm />} />

          <Route path="admin/sections/import" element={<ImportSections />} />
          <Route path="admin/sections/add-students-bulk" element={<BulkAddStudents />} />         

          {/* Training Sites */}
          <Route path="/admin/training-sites" element={<TrainingSitesList />} />
          <Route path="/admin/training-sites/create" element={<TrainingSiteForm />} />
          <Route path="/admin/training-sites/edit/:id" element={<TrainingSiteForm />} />

          {/* Training Periods */}
          <Route path="/admin/training-periods" element={<TrainingPeriodsList />} />
          <Route path="/admin/training-periods/create" element={<TrainingPeriodForm />} />
          <Route path="/admin/training-periods/edit/:id" element={<TrainingPeriodForm />} />

          {/* Announcements */}
          <Route path="/admin/announcements" element={<AnnouncementsList />} />
          <Route path="/admin/announcements/create" element={<AnnouncementForm />} />
          <Route path="/admin/announcements/edit/:id" element={<AnnouncementForm />} />

          {/* System */}
          <Route path="/admin/backups" element={<BackupsList />} />
          <Route path="/admin/backups/:id" element={<BackupDetails />} />
          <Route path="/admin/activity-logs" element={<ActivityLogsList />} />
          <Route path="/admin/feature-flags" element={<FeatureFlagsList />} />

          {/* Evaluation Templates */}
          <Route path="/admin/evaluation-templates" element={<EvaluationTemplatesList />} />
          <Route path="/admin/evaluation-templates/create" element={<EvaluationTemplateForm />} />
          <Route path="/admin/evaluation-templates/edit/:id" element={<EvaluationTemplateForm />} />

          {/* Reports */}
          <Route path="/reports" element={<ReportsDashboard />} />

          {/* Student Portal */}
          <Route path="/student/dashboard" element={<StudentDashboard />} />
          <Route path="/student/schedule" element={<Schedule />} />
          <Route path="/student/portfolio" element={<Portfolio />} />
          <Route path="/student/training-log" element={<TrainingLog />} />
          <Route path="/student/assignments" element={<Assignments />} />
          <Route path="/student/training-request" element={<TrainingRequest />} />
          <Route path="/student/notifications-updates" element={<NotificationsUpdates />} />

          {/* Supervisor Portal */}
          <Route path="/supervisor/dashboard" element={<SupervisorDashboard />} />
          <Route path="/supervisor/tasks" element={<Tasks />} />
          <Route path="/supervisor/field-visits" element={<FieldVisits />} />
          <Route path="/supervisor/sections" element={<Sections />} />
          <Route path="/supervisor/evaluations" element={<Evaluations />} />
          <Route path="/supervisor/reports" element={<SupervisorReports />} />
          <Route path="/supervisor/submissions" element={<Submissions />} />

          {/* Mentor Portal */}
          <Route path="/mentor/dashboard" element={<MentorDashboard />} />
          <Route path="/mentor/attendance" element={<Attendance />} />

          {/* Coordinator Portal */}
          <Route path="/coordinator/dashboard" element={<CoordinatorDashboard />} />

          {/* Principal Portal */}
          <Route path="/principal/dashboard" element={<PrincipalDashboard />} />
          <Route path="/principal/profile" element={<PrincipalProfile />} />
          <Route path="/principal/mentor-assignment" element={<MentorAssignment />} />
          <Route path="/principal/trainee-students" element={<TraineeStudents />} />
          <Route path="/principal/official-letters" element={<PrincipalOfficialLetters />} />

          {/* Health Directorate Portal */}
          <Route path="/health/dashboard" element={<HealthDirectorateDashboard />} />

          {/* Education Directorate Portal */}
          <Route path="/education/dashboard" element={<EducationDirectorateDashboard />} />
          <Route path="/education/training-sites" element={<EducationTrainingSites />} />
          <Route path="/education/official-letters" element={<EducationOfficialLetters />} />

          {/* Common */}
          <Route path="/profile" element={<Profile />} />
          <Route path="/change-password" element={<ChangePassword />} />
          <Route path="/notifications" element={<Notifications />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}