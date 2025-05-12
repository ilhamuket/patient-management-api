<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PatientService;
use App\Traits\ApiResponseTrait;
use App\Transformers\PatientTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Patient Management API Documentation",
 *      description="API Documentation for managing patients",
 *      @OA\Contact(
 *          email="muhamad.ilham@example.com"
 *      )
 * )
 * 
 * @OA\Server(
 *      url="/api",
 *      description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *      securityScheme="ApiKeyAuth",
 *      type="apiKey",
 *      in="header",
 *      name="X-API-KEY"
 * )
 */
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
     * @OA\Get(
     *      path="/patients",
     *      operationId="getPatientsList",
     *      tags={"Patients"},
     *      summary="Get list of patients",
     *      description="Returns list of patients",
     *      security={{"ApiKeyAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Patients retrieved successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="John Doe"),
     *                      @OA\Property(property="id_type", type="string", example="KTP"),
     *                      @OA\Property(property="id_no", type="string", example="1234567890"),
     *                      @OA\Property(property="gender", type="string", example="male"),
     *                      @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                      @OA\Property(property="address", type="string", example="123 Main St"),
     *                      @OA\Property(property="medium_acquisition", type="string", example="website"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function index()
    {
        try {
            $patients = $this->patientService->getAllPatients();
            $transformedPatients = $this->patientTransformer->transformCollection($patients);
            
            return $this->successResponse($transformedPatients, 'Here are the patients you requested!');
        } catch (\Exception $e) {
            return $this->errorResponse('Oops! Something went wrong while retrieving patients. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     *      path="/patients",
     *      operationId="storePatient",
     *      tags={"Patients"},
     *      summary="Create new patient",
     *      description="Returns the newly created patient",
     *      security={{"ApiKeyAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","id_type","id_no","gender","dob","address","medium_acquisition"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="id_type", type="string", example="KTP"),
     *              @OA\Property(property="id_no", type="string", example="1234567890"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
     *              @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *              @OA\Property(property="address", type="string", example="123 Main St"),
     *              @OA\Property(property="medium_acquisition", type="string", example="website"),
     *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Patient created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Patient created successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="id_type", type="string", example="KTP"),
     *                  @OA\Property(property="id_no", type="string", example="1234567890"),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="medium_acquisition", type="string", example="website"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function store(Request $request)
    {
        try {
            $patient = $this->patientService->createPatient($request->all());
            $transformedPatient = $this->patientTransformer->transform($patient);
            
            return $this->successResponse($transformedPatient, 'Patient successfully created!', Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->errorResponse('Oops! There were some issues with the input. Please check and try again.', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse('Oops! Something went wrong while creating the patient. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *      path="/patients/{id}",
     *      operationId="getPatientById",
     *      tags={"Patients"},
     *      summary="Get patient information",
     *      description="Returns patient data",
     *      security={{"ApiKeyAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Patient id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Patient retrieved successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="id_type", type="string", example="KTP"),
     *                  @OA\Property(property="id_no", type="string", example="1234567890"),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="medium_acquisition", type="string", example="website"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function show(string $id)
    {
        try {
            $patient = $this->patientService->getPatientById($id);
            if (!$patient) {
                return $this->errorResponse('Patient not found. Please verify the ID and try again.', Response::HTTP_NOT_FOUND);
            }
            $transformedPatient = $this->patientTransformer->transform($patient);
            
            return $this->successResponse($transformedPatient, 'Patient details retrieved successfully!');
        } catch (\Exception $e) {
            return $this->errorResponse('Oops! Something went wrong while retrieving the patient. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *      path="/patients/{id}",
     *      operationId="updatePatient",
     *      tags={"Patients"},
     *      summary="Update existing patient",
     *      description="Returns updated patient data",
     *      security={{"ApiKeyAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Patient id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","id_type","id_no","gender","dob","address","medium_acquisition"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="id_type", type="string", example="KTP"),
     *              @OA\Property(property="id_no", type="string", example="1234567890"),
     *              @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
     *              @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *              @OA\Property(property="address", type="string", example="123 Main St"),
     *              @OA\Property(property="medium_acquisition", type="string", example="website")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Patient updated successfully"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="id_type", type="string", example="KTP"),
     *                  @OA\Property(property="id_no", type="string", example="1234567890"),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                  @OA\Property(property="address", type="string", example="123 Main St"),
     *                  @OA\Property(property="medium_acquisition", type="string", example="website"),
     *                  @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00+00:00")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $patient = $this->patientService->updatePatient($id, $request->all());
            if (!$patient) {
                return $this->errorResponse('Patient not found. Please verify the ID and try again.', Response::HTTP_NOT_FOUND);
            }
            $transformedPatient = $this->patientTransformer->transform($patient);
            
            return $this->successResponse($transformedPatient, 'Patient updated successfully!');
        } catch (ValidationException $e) {
            return $this->errorResponse('Oops! There were some issues with the input. Please check and try again.', Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return $this->errorResponse('Oops! Something went wrong while updating the patient. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *      path="/patients/{id}",
     *      operationId="deletePatient",
     *      tags={"Patients"},
     *      summary="Delete a patient",
     *      description="Deletes a patient by ID",
     *      security={{"ApiKeyAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Patient id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Patient deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Server Error"
     *      )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $patient = $this->patientService->deletePatient($id);
            if (!$patient) {
                return $this->errorResponse('Patient not found. Please verify the ID and try again.', Response::HTTP_NOT_FOUND);
            }
            
            return $this->successResponse([], 'Patient deleted successfully!');
        } catch (\Exception $e) {
            return $this->errorResponse('Oops! Something went wrong while deleting the patient. Please try again later.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}