<?php
namespace App\Controllers;

use App\Classes\Intervals\Interfaces\IntervalActionsInterface;
use App\Classes\Intervals\IntervalDispatcher;
use App\Models\Interval;
use Core\Config\ValidatorFactory;
use Core\Controller;
use Core\Database\IntervalValue;
use Core\Database\ModelRecord;
use Core\Header;
use Symfony\Component\HttpFoundation\Response;

class IntervalController extends Controller implements IntervalActionsInterface
{
    public function index()
    {
        $interval = $this->getModel('Interval');

        $data = $interval->getAll();

        echo $this->twig->render('index.twig', [
            'ranges' => $data,
        ]);

    }

    public function getOne($id)
    {
        $header = $this->container->get(Header::class);
        $interval = $this->getModel('Interval');
        $data = $interval->getOne($id, Interval::FETCH_ARR);

        $data = [
            'status' => 'ok',
            'data' => $data,
        ];
        $header->send($data);
        return true;
    }

    public function getAll()
    {
        $header = $this->container->get(Header::class);
        $interval = $this->getModel('Interval');

        $data = $interval->getAll(Interval::FETCH_ARR);

        $data = [
            'status' => 'ok',
            'data' => $data,
        ];
        $header->send($data);
        return true;

    }

    public function deleteAll()
    {
        $header = $this->container->get(Header::class);
        $interval = $this->getModel('Interval');

        $interval->deleteAll();

        $data = [
            'status' => 'ok',
            'data' => [],
        ];
        $header->send($data);
        return true;
    }


    public function deleteOne($id)
    {
        $header = $this->container->get(Header::class);
        $interval = $this->getModel('Interval');

        $rows = $interval->delete([$id]);

        if(!$rows) {
            $data = [
                'status' => 'error',
                'data' => [],
            ];
            $header->sendCode($data, Response::HTTP_BAD_REQUEST);
            return true;
        }

        $data = [
            'status' => 'ok',
            'data' => [],
        ];
        $header->send($data);
        return true;
    }


    public function edit()
    {
        $formStartDate = $this->request->get('start_date');
        $formEndDate = $this->request->get('end_date');
        $formPrice = $this->request->get('price');
        $formId = $this->request->get('id');

        $header = $this->container->get(Header::class);
        $fields = [
            'start_date' => $formStartDate,
            'end_date' => $formEndDate,
            'price' => $formPrice,
            'id' => $formId,
        ];

        $validator = (new ValidatorFactory())->make($fields, [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'price'      => 'required|numeric|min:0.01',
            'id'         => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $header->sendCode(['status'=>'error',
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var $intervalModel \App\Models\Interval
         */
        $intervalModel = $this->getModel('Interval');
        $data = $intervalModel->getIntervals($formStartDate, $formEndDate);

        $newInterval = new IntervalValue([
            'id' => 0,
            'start_date' => (new \DateTime($formStartDate))->format(ModelRecord::DEFAULT_DATE_FORMAT),
            'end_date' => (new \DateTime($formEndDate))->format(ModelRecord::DEFAULT_DATE_FORMAT),
            'price' => $formPrice
        ]);

        $dispatcher = new IntervalDispatcher($newInterval);

        foreach ($data as $dbInterval) {
            if ($dbInterval->getId() == $formId) {
                $dbInterval->setAction(self::DELETE_ACTION);
                $dispatcher->injectInterval($dbInterval);
            } else {
                $dispatcher->addInterval($dbInterval);
            }

        }

        $intervals = $dispatcher->getIntervals();

        try {
            $intervalModel->beginTransaction();

            foreach ($intervals as $intervalObj) {
                switch ($intervalObj->getAction()) {
                    case self::INSERT_ACTION :
                        $intervalModel->add($intervalObj);
                        break;
                    case self::DELETE_ACTION :
                        $intervalModel->delete([$intervalObj]);
                        break;
                    case self::UPDATE_ACTION :
                        $intervalModel->edit($intervalObj);
                        break;
                }
            }
            $intervalModel->commit();

            $this->getAll();

        } catch (\Exception $e){

            //Rollback the transaction.
            $intervalModel->rollBack();

            $header = $this->container->get(Header::class);

            $data = [
                'status' => 'error',
                'data' => $e->getMessage(),
            ];
            $header->sendCode($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return true;

    }


    public function add()
    {
        $startDate = $this->request->get('start_date');
        $endDate = $this->request->get('end_date');
        $price = $this->request->get('price');

        $header = $this->container->get(Header::class);
        $fields = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price,
        ];

        $validator = (new ValidatorFactory())->make($fields, [
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'price'      => 'required|numeric|min:0.01'
        ]);

        if ($validator->fails()) {
            return $header->sendCode(['status'=>'error',
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var $intervalModel \App\Models\Interval
         */
        $intervalModel = $this->getModel('Interval');
        $data = $intervalModel->getIntervals($startDate, $endDate);

        $newInterval = new IntervalValue([
            'id' => 0,
            'start_date' => (new \DateTime($startDate))->format(ModelRecord::DEFAULT_DATE_FORMAT),
            'end_date' => (new \DateTime($endDate))->format(ModelRecord::DEFAULT_DATE_FORMAT),
            'price' => $price
        ]);

        $dispatcher = new IntervalDispatcher($newInterval);

        foreach ($data as $id => $dbInterval) {
            $dispatcher->addInterval($dbInterval);
        }

        $intervals = $dispatcher->getIntervals();

        try {
            $intervalModel->beginTransaction();

            foreach ($intervals as $intervalObj) {
                switch ($intervalObj->getAction()) {
                    case self::INSERT_ACTION :
                        $intervalModel->add($intervalObj);
                        break;
                    case self::DELETE_ACTION :
                        $intervalModel->delete([$intervalObj]);
                        break;
                    case self::UPDATE_ACTION :
                        $intervalModel->edit($intervalObj);
                        break;
                }
            }
            $intervalModel->commit();

            $this->getAll();

        } catch (\Exception $e){

            //Rollback the transaction.
            $intervalModel->rollBack();

            $header = $this->container->get(Header::class);

            $data = [
                'status' => 'error',
                'data' => $e->getMessage(),
            ];
            $header->sendCode($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return true;
    }
}