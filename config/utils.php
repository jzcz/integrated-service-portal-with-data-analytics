<?php 
    require __DIR__ . '/../vendor/autoload.php';
    
    use Hidehalo\Nanoid\Client;
    use Hidehalo\Nanoid\GeneratorInterface;

    function generateNanoId($size = 10) {
        $client = new Client();
        $nanoid = $client->generateId($size); 
        return $nanoid;
    }

    function sanitizeData($conn, $data) {
        $data = trim($data);
        $data = $conn->real_escape_string($data);
        return $data;
    }

    function generateFileName($name) {
        $uniqueId = generateNanoId(5); // Generate a unique ID
        $fileName = $name . '_' . $uniqueId;
        return $fileName;
    }

    function getProgramNameById($db_conn, $programId) {
        $qry = "SELECT program_name FROM programs WHERE program_id = ?;";
        $stmt = $db_conn->prepare($qry);
        if ($stmt) {
            $stmt->bind_param("i", $programId);
            $stmt->execute();
            $result = $stmt->get_result();
            $program = $result->fetch_assoc();
            return $program['program_name'] ?? null;
            $stmt->close();
        } else {
            return null;
        }
    }
?>