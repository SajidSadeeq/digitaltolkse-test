<?php

declare(strict_types=1);

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use Throwable;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * BookingController constructor.
     * @param BookingRepository $repository
     */
    public function __construct(private BookingRepository $repository)
    {

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try{

            $user_id = $request->get('user_id');
            if($user_id) { 
                $response = $this->repository->getUsersJobs($user_id);
            }
            $user_type = $request->__authenticatedUser->user_type;
            if($user_type == env('ADMIN_ROLE_ID') || $user_type == env('SUPERADMIN_ROLE_ID'))
            {
                $response = $this->repository->getAll($request);
            }
    
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show(int $id)
    {
        try{

            $job = $this->repository->with('translatorJobRel.user')->find($id);
            return response()->json($job, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try{
            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->store($request->__authenticatedUser, $data);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update(int $id, Request $request)
    {
        try{
            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $request->__authenticatedUser);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try{

            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->storeJobEmail($data);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try{
            
            $user_id = $request->get('user_id');
            if($user_id) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response()->json($response, 200);
            }
            return null;

        } catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try{

            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->acceptJob($data, $request->__authenticatedUser);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptJobWithId(Request $request)
    {
        try{

            $response = $this->repository->acceptJobWithId($request->get('job_id'), $request->__authenticatedUser);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try{
            $response = $this->repository->cancelJobAjax($request->only(['param1', 'param2']), $request->__authenticatedUser);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try{

            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->endJob($data);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function customerNotCall(Request $request)
    {
        try{

            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->customerNotCall($data);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try{
            $response = $this->repository->getPotentialJobs($request->__authenticatedUser);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function distanceFeed(Request $request)
    {
        try{

            $data = $request->all();

            $flagged = 'no';
            if ($data['flagged'] == 'true') {
                if($data['admincomment'] == '')
                    return response()->json("Please, add comment", 204);
                $flagged = 'yes';
            }
    
            $distance = "";
            if (isset($data['distance']) && $data['distance'] != "") {
                $distance = $data['distance'];
            }

            $time = "";
            if (isset($data['time']) && $data['time'] != "") {
                $time = $data['time'];
            }

            if (isset($data['jobid']) && $data['jobid'] != "") {
                $jobid = $data['jobid'];
            }
            
            $session = "";
            if (isset($data['session_time']) && $data['session_time'] != "") {
                $session = $data['session_time'];
            }
            
            $manually_handled = 'no';
            if ($data['manually_handled'] == 'true') {
                $manually_handled = 'yes';
            }
            
            $by_admin = 'no';
            if ($data['by_admin'] == 'true') {
                $by_admin = 'yes';
            }
    
            $admincomment = "";
            if (isset($data['admincomment']) && $data['admincomment'] != "") {
                $admincomment = $data['admincomment'];
            }

            if ($time || $distance) {
    
                Distance::where('job_id', '=', $jobid)
                    ->update(array('distance' => $distance, 'time' => $time));
            }
    
            if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
    
                Job::where('id', '=', $jobid)->update(
                    array(
                        'admin_comments' => $admincomment,
                        'flagged' => $flagged, 'session_time' => $session,
                        'manually_handled' => $manually_handled,
                        'by_admin' => $by_admin
                    )
                );
    
            }
            return response()->json('Record updated!', 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function reopen(Request $request)
    {
        try{

            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $response = $this->repository->reopen($data);
            return response()->json($response, 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resendNotifications(Request $request)
    {
        try{

            $data = $request->only(['param1', 'param2']);    //  $request->all() is insecure
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            return response()->json(['success' => 'Push sent'], 200);

        }catch(Throwable $e)
        {
            return response()->json($e->getMessage(), 204);
        }
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {

            $job = $this->repository->find($request->only(['param1', 'param2'])); //  $request->all() is insecure
            $this->repository->jobToData($job);
            $this->repository->sendSMSNotificationToTranslator($job);
            return response()->json(['success' => 'SMS sent'], 200);

        } catch (\Exception $e) {
            return response()->json(['success' => $e->getMessage()], 204);
        }
    }

}
