<?php
// src/controllers/IncidentController.php

require_once __DIR__ . '/../models/Incident.php';
require_once __DIR__ . '/../config/db.php';

class IncidentController {
    private $db;
    private $incidentModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->incidentModel = new Incident($this->db);
    }

    public function reportIncident($data) {
        // Ensure user is logged in
        session_start();
        if (!isset($_SESSION['user_id'])) {
            return ['success' => false, 'message' => 'User not logged in.'];
        }

        $this->incidentModel->incident_id = $this->incidentModel->generateIncidentId();
        $this->incidentModel->title = $data['title'];
        $this->incidentModel->description = $data['description'];
        $this->incidentModel->incident_type = $data['incident_type'];
        $this->incidentModel->severity = $data['severity'];
        $this->incidentModel->reported_by_user_id = $_SESSION['user_id'];
        $this->incidentModel->status = 'New'; // Default status

        if ($this->incidentModel->create()) {
            return ['success' => true, 'message' => 'Incident reported successfully!', 'incident_id' => $this->incidentModel->incident_id];
        } else {
            return ['success' => false, 'message' => 'Failed to report incident.'];
        }
    }

    public function getIncidents() {
        return $this->incidentModel->read();
    }

    public function getSingleIncident($id) {
        return $this->incidentModel->readSingle($id);
    }

    public function searchIncidents($incident_id) {
        return $this->incidentModel->searchById($incident_id);
    }

    // New feature for Part C
    public function updateIncidentStatus($incidentId, $status, $resolutionNotes, $resolutionDate = null) {
        // Ensure user is authorized (e.g., admin or responder)
        session_start();
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'responder')) {
            return ['success' => false, 'message' => 'Unauthorized access.'];
        }

        $this->incidentModel->id = $incidentId;
        $this->incidentModel->status = $status;
        $this->incidentModel->resolution_notes = $resolutionNotes;
        $this->incidentModel->resolution_date = $resolutionDate ?: date('Y-m-d H:i:s'); // Set current time if not provided

        if ($this->incidentModel->updateStatusAndResolution()) {
            return ['success' => true, 'message' => 'Incident status updated successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to update incident status.'];
        }
    }
}
?>