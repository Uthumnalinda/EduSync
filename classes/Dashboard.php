<?php
/**
 * Dashboard Service Class (OOP)
 * Queries MySQL Database in Real-Time for statistics, metrics, student records, and chart analytics.
 */

require_once __DIR__ . '/../config/Database.php';

class Dashboard {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Fetch key performance indicator (KPI) metric counts live from MySQL database
     */
    public function getMetrics() {
        if ($this->db !== null) {
            try {
                $totalStudents = $this->db->query("SELECT COUNT(*) FROM students")->fetchColumn();
                $activeTeachers = $this->db->query("SELECT COUNT(*) FROM teachers WHERE status = 'Active'")->fetchColumn();
                $totalCourses = $this->db->query("SELECT COUNT(*) FROM courses")->fetchColumn();
                $activeEnrollments = $this->db->query("SELECT COUNT(*) FROM enrollments WHERE status IN ('Enrolled', 'Active')")->fetchColumn();
                
                return [
                    'total_students' => (int)$totalStudents,
                    'active_teachers' => (int)$activeTeachers,
                    'total_courses' => (int)$totalCourses,
                    'active_enrollments' => (int)$activeEnrollments
                ];
            } catch (Exception $e) {
                // Fallback to default metrics if database read fails
            }
        }

        // Fallback default values
        return [
            'total_students' => 8,
            'active_teachers' => 5,
            'total_courses' => 6,
            'active_enrollments' => 8
        ];
    }

    /**
     * Get monthly enrollment trend data for area chart
     */
    public function getEnrollmentTrends() {
        if ($this->db !== null) {
            try {
                // Query real monthly registration counts directly from MySQL students table
                $stmt = $this->db->query("
                    SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS students 
                    FROM students 
                    GROUP BY MONTH(created_at), DATE_FORMAT(created_at, '%b') 
                    ORDER BY MONTH(created_at) ASC
                ");
                $trends = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($trends)) {
                    foreach ($trends as &$t) {
                        $t['students'] = (int)$t['students'];
                    }
                    return $trends;
                }
            } catch (Exception $e) {}
        }

        return [];
    }

    /**
     * Get distribution of courses by enrollment count live from MySQL
     */
    public function getCourseDistribution() {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("
                    SELECT c.course_name AS name, COUNT(e.id) AS value 
                    FROM courses c 
                    LEFT JOIN enrollments e ON c.id = e.course_id 
                    GROUP BY c.id, c.course_name 
                    ORDER BY value DESC 
                    LIMIT 5
                ");
                $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($courses)) {
                    $colors = ['#2563eb', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444', '#0d9488'];
                    $i = 0;
                    foreach ($courses as &$c) {
                        $c['value'] = (int)$c['value'];
                        $c['color'] = $colors[$i % count($colors)];
                        $i++;
                    }
                    return $courses;
                }
            } catch (Exception $e) {}
        }

        return [
            ['name' => 'Mathematics', 'value' => 62, 'color' => '#2563eb'],
            ['name' => 'English', 'value' => 48, 'color' => '#10b981'],
            ['name' => 'Biology', 'value' => 41, 'color' => '#f59e0b'],
            ['name' => 'Physics', 'value' => 38, 'color' => '#8b5cf6'],
            ['name' => 'History', 'value' => 29, 'color' => '#ef4444'],
        ];
    }

    /**
     * Get grade breakdown live from MySQL database
     */
    public function getGradeBreakdown() {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("
                    SELECT grade, COUNT(*) AS count 
                    FROM students 
                    GROUP BY grade 
                    ORDER BY grade ASC
                ");
                $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($grades)) {
                    $formatted = [];
                    foreach ($grades as $g) {
                        $label = str_replace('Grade ', 'G', $g['grade']);
                        $formatted[] = [
                            'grade' => $label,
                            'count' => (int)$g['count']
                        ];
                    }
                    return $formatted;
                }
            } catch (Exception $e) {}
        }

        return [];
    }

    /**
     * Get recent students list live from MySQL database
     */
    public function getRecentStudents() {
        if ($this->db !== null) {
            try {
                $stmt = $this->db->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($students)) {
                    $formatted = [];
                    $colors = ['#2563eb', '#d97706', '#8b5cf6', '#0d9488', '#2563eb'];
                    $i = 0;
                    foreach ($students as $s) {
                        $name = isset($s['full_name']) ? $s['full_name'] : (isset($s['first_name']) ? trim($s['first_name'] . ' ' . $s['last_name']) : 'Student');
                        $code = isset($s['adm_no']) ? $s['adm_no'] : (isset($s['admission_no']) ? $s['admission_no'] : (isset($s['student_code']) ? $s['student_code'] : 'ADM2024'));
                        $gradeStr = isset($s['grade']) ? $s['grade'] : 'Grade 12';
                        if (strpos($gradeStr, 'Grade') === false) {
                            $gradeStr = 'Grade ' . $gradeStr;
                        }
                        $statusStr = isset($s['status']) ? $s['status'] : 'Active';

                        $parts = explode(' ', $name);
                        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));

                        $formatted[] = [
                            'name' => $name,
                            'code' => $code,
                            'grade' => $gradeStr,
                            'status' => $statusStr,
                            'bg' => $colors[$i % count($colors)],
                            'initials' => $initials
                        ];
                        $i++;
                    }
                    return $formatted;
                }
            } catch (Exception $e) {}
        }

        return [];
    }

    /**
     * Get live unread notifications dynamically from MySQL (auto-detecting new students)
     */
    public function getNotifications() {
        if ($this->db !== null) {
            try {
                // Auto-detect any newly added students that don't have a notification entry yet
                $stmt = $this->db->query("
                    SELECT s.id, s.first_name, s.last_name, s.grade, s.status 
                    FROM students s
                    LEFT JOIN notifications n ON n.ref_student_id = s.id
                    WHERE n.id IS NULL
                ");
                $newStudents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($newStudents)) {
                    $insStmt = $this->db->prepare("
                        INSERT INTO notifications (title, message, type, is_read, ref_student_id) 
                        VALUES (:title, :message, :type, 0, :ref_student_id)
                    ");
                    foreach ($newStudents as $ns) {
                        $name = trim($ns['first_name'] . ' ' . $ns['last_name']);
                        $isInactive = (strtolower($ns['status']) === 'inactive');
                        $insStmt->execute([
                            ':title' => $isInactive ? 'Student Status Updated' : 'New Student Enrolled',
                            ':message' => $isInactive ? "$name's status set to {$ns['status']}" : "$name registered in {$ns['grade']}",
                            ':type' => $isInactive ? 'amber' : 'green',
                            ':ref_student_id' => $ns['id']
                        ]);
                    }
                }
            } catch (Exception $e) {}

            try {
                // Query unread notifications directly from MySQL notifications table
                $stmt = $this->db->query("
                    SELECT * FROM notifications 
                    WHERE is_read = 0 
                    ORDER BY id DESC 
                    LIMIT 5
                ");
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($items)) {
                    $notifications = [];
                    foreach ($items as $n) {
                        $dotColor = 'dot-green';
                        if ($n['type'] === 'amber') $dotColor = 'dot-amber';
                        if ($n['type'] === 'blue')  $dotColor = 'dot-blue';
                        
                        $notifications[] = [
                            'id'    => $n['id'],
                            'title' => $n['title'],
                            'desc'  => $n['message'],
                            'time'  => 'Recently',
                            'dot'   => $dotColor
                        ];
                    }
                    return $notifications;
                }
            } catch (Exception $e) {}
        }

        return [];
    }
}
?>
