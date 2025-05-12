<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PatientService;
use App\Traits\ApiResponseTrait;
use App\Transformers\PatientTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    use ApiResponseTrait;

    protected $patientService;
    protected $patientTransformer;

    public function __construct(PatientService $patientService, PatientTransformer $patientTransformer)
    {
        $this->patientService = $patientService;
        $this->patientTransformer = $patientTransformer;
    }

    /**
     * Display a listing of patients.
     */
    public function index()
    {
        try {
            $patients = $this->patientService->getAllPatients();
            $transformedPatients = $this->patientTransformer->transformCollection($patients);
            
            return $this->successResponse($transformedPatients, 'Patients retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(Request $request)
    {
        try {
            $patient = $this->patientService->createPatient($request->all());
            $transformedPatient = $this->patientTransformer->transform($patient);
            
            return $this->successResponse($transformedPatient, 'Patient created successfully', Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified patient.
     */
    public function show(string $id)
    {
        try {
            $patient = $this->patientService->getPatientById($id);
            $transformedPatient = $this->patientTransformer->transform($patient);
            
            return $this->successResponse($transformedPatient, 'Patient retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $patient = $this->patientService->updatePatient($id, $request->all());
            $transformedPatient = $this->patientTransformer->transform($patient);
            
            return $this->successResponse($transformedPatient, 'Patient updated successfully');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->patientService->deletePatient($id);
            
            return $this->noContentResponse('Patient deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}