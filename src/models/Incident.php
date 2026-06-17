<?php
// src/models/Incident.php

class Incident {
    private $conn;
    private $table = 'incidents';

    public $id;
    public $incident_id;
    public $title;
    public $description;
    public $incident_type;
    public $severity;
    public $status;
    public $reported_by_user_id;
    public $report_date;
    public $last_updated_date;
    public $resolution_notes;
    public $resolution_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Generate a unique incident ID (e.g., INC-YYYY-SEQ)
    public function generateIncidentId() {
        $year = date('Y');
        $query = "SELECT COUNT(*) AS count FROM " . $this->table . " WHERE incident_id LIKE 'INC-" . $year . "-%'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'] + 1;
        return "INC-" . $year . "-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // Create a new incident
    public function create() {
        $query = "INSERT INTO " . $this->table . " (incident_id, title, description, incident_type, severity, status, reported_by_user_id) VALUES (:incident_id, :title, :description, :incident_type, :severity, :status, :reported_by_user_id)";
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind values
        $this->incident_id = htmlspecialchars(strip_tags($this->incident_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->incident_type = htmlspecialchars(strip_tags($this->incident_type));
        $this->severity = htmlspecialchars(strip_tags($this->severity));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->reported_by_user_id = htmlspecialchars(strip_tags($this->reported_by_user_id));

        $stmt->bindParam(':incident_id', $this->incident_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':incident_type', $this->incident_type);
        $stmt->bindParam(':severity', $this->severity);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':reported_by_user_id', $this->reported_by_user_id);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Read all incidents
    public function read() {
        $query = "SELECT i.id, i.incident_id, i.title, i.incident_type, i.severity, i.status, i.report_date, u.username as reported_by_username
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.reported_by_user_id = u.id
                  ORDER BY i.report_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single incident by ID
    public function readSingle($id) {
        $query = "SELECT i.id, i.incident_id, i.title, i.description, i.incident_type, i.severity, i.status, i.report_date, i.last_updated_date, i.resolution_notes, i.resolution_date, u.username as reported_by_username
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.reported_by_user_id = u.id
                  WHERE i.id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Search incidents by incident_id
    public function searchById($incident_id) {
        $query = "SELECT i.id, i.incident_id, i.title, i.incident_type, i.severity, i.status, i.report_date, u.username as reported_by_username
                  FROM " . $this->table . " i
                  LEFT JOIN users u ON i.reported_by_user_id = u.id
                  WHERE i.incident_id LIKE :incident_id ORDER BY i.report_date DESC";
        $stmt = $this->conn->prepare($query);
        $search_param = '%' . $incident_id . '%';
        $stmt->bindParam(':incident_id', $search_param);
        $stmt->execute();
        return $stmt;
    }

    // Update incident status and resolution (Part C new feature later)
    public function updateStatusAndResolution() {
        $query = "UPDATE " . $this->table . " SET status = :status, resolution_notes = :resolution_notes, resolution_date = :resolution_date WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->resolution_notes = htmlspecialchars(strip_tags($this->resolution_notes));
        $this->resolution_date = htmlspecialchars(strip_tags($this->resolution_date));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind parameters
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':resolution_notes', $this->resolution_notes);
        $stmt->bindParam(':resolution_date', $this->resolution_date);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
}
?>