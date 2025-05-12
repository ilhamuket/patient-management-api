<?php

namespace App\Repositories\Interfaces;

interface PatientRepositoryInterface
{
    public function getAllPatients();
    public function getPatientById($id);
    public function createPatient(array $data);
    public function updatePatient($id, array $data);
    public function deletePatient($id);
}