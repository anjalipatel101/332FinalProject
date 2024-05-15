<?php
     $dbServerName = "mariadb";
     $userName = "cs332c9"; // account username
     $password = "dsSW1nL8"; // account password
     $dbName = "cs332c9"; //database name

     // Create connection
    $conn = mysqli_connect($dbServerName, $userName, $password, $dbName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    echo 'Connected sucessfully<p>';

    //initialize and set to null if recieve through POST method
    $professorSSN = isset($_POST['prof_Ssn']) ? $_POST['prof_Ssn'] : null;
    $courseNum = isset($_POST['course_Num']) ? $_POST['course_Num'] : null;
    $sectionNum = isset($_POST['sec_Num']) ? $_POST['sec_Num'] : null;
    $studentCWID = isset($_POST['stud_Cwid']) ? $_POST['stud_Cwid'] : null;
    $studentCourseNum = isset($_POST['student_course_num']) ? $_POST['student_course_num'] : null;
    //Given SSN (Professor)
    if (!empty($professorSSN)) {
        $query = "SELECT Course.Title, Section.meeting_days, Section.start_time, Section.end_time
            FROM Professor
            JOIN Section ON Professor.SSN = Section.PSSN
            JOIN Course ON Section.CNO = Course.course_number
            WHERE Professor.SSN = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $professorSSN);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "Title: " . $row["Title"]. "- Days: " . $row["meeting_days"]. "- Start Time: " . $row["start_time"]. "- End Time: " . $row["end_time"]. "<br>";
            }
        $stmt->close();
    }

    //Given Course Num and Sec Num (Professor)
    if (!empty($courseNum)&& !empty($sectionNum)) {
        echo "ALSKJFLDJAFLJSDFLKJFDLKJFS <br>";
        $query = "SELECT EnrollmentRecord.Grade, COUNT(*) as count
            FROM EnrollmentRecord
            WHERE EnrollmentRecord.course_num = ?  AND EnrollmentRecord.section_number = ?
            GROUP BY EnrollmentRecord.Grade";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $courseNum, $sectionNum);
    $stmt->execute();
    $result = $stmt->get_result();

    echo $result;

    while ($row = $result->fetch_assoc()) {
        echo "Grade: " . $row["Grade"]. "- Count: " . $row["count"]. "<br>";
        }
    $stmt->close();
    }

    //Given Course Number (student)
    if (!empty($studentCourseNum)) {
        $query = "SELECT Section.section_number, Section.meeting_days, Section.start_time, Section.end_time, COUNT(EnrollmentRecord.CWID) AS student_enrolled
            FROM Section
            JOIN Course ON Section.CourseNum = Course.course_number
            LEFT JOIN EnrollmentRecord ON Section.section_number = EnrollmentRecord.SecNum
            WHERE Course.course_number = ?
            GROUP BY Section.section_number";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $studentCourseNum);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "Section Number: " . $row["section_number"]. "- Days: " . $row["meeting_days"]. "- Start Time: " . $row["start_time"]. "- End Time: " . $row["end_time"]. "- Enrolled Students: " . $row["student_enrolled"]. "<br>";
        }
        $stmt->close();
    }

    //Given CWID (Student)
    if (!empty($studentCWID)) {
        $query = "SELECT Course.Title, EnrollmentRecord.Grade
            FROM Student
            JOIN EnrollmentRecord ON Student.CWID = EnrollmentRecord.CWID
            JOIN Section ON EnrollmentRecord.SecNum = Section.section_number
            JOIN Course ON Section.CNO = Course.course_number
            WHERE Student.CWID = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $studentCWID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "Course: " . $row["Title"]. "- Grade: " . $row["Grade"]. "<br>";
        }
        $stmt->close();
    }
    //close connection
    $conn->closed();
?>
