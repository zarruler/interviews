<?php
namespace App\Controllers;

use App\Classes\Intervals\Interfaces\IntervalActionsInterface;
use App\Classes\Intervals\IntervalDispatcher;
use App\Models\Interval;
use Core\Config\ValidatorFactory;
use Core\Controller;
use Core\Database\IntervalValue;
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
            'end_date'   => 'required|date|after:start_date',
            'price'      => 'required|numeric|min:0.01'
        ] );

        if ($validator->fails()) {
            return $header->sendCode(['status'=>'error',
                'errors'=>$validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var $intervalModel \App\Models\Interval
         */
        $intervalModel = $this->getModel('Interval');
        $data = $intervalModel->getIntervals($startDate, $endDate);
//var_dump($data);
//die;

        $newInterval = new IntervalValue([
            'id' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'price' => $price
        ]);
//var_dump($newInterval);
//die;
        $dispatcher = new IntervalDispatcher($newInterval);

        foreach ($data as $id => $dbInterval) {
            $dispatcher->addInterval($dbInterval);
        }

        $intervals = $dispatcher->getIntervals();

        foreach ($intervals as $intervalObj)
        {
            switch ($intervalObj->getAction()){
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

        $this->getAll();
    }
}