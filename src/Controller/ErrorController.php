<?php

namespace App\Controller;

use App\Class\SearchError;
use App\Form\SearchErrorType;
use App\Repository\ErrorFamilyRepository;
use App\Repository\ErrorRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ErrorController extends AbstractController
{
    public $backgroundArray = [
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(255, 205, 86, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(201, 203, 207, 0.2)'
    ];
      
    public $borderArray = [
        'rgb(255, 99, 132)',
        'rgb(255, 159, 64)',
        'rgb(255, 205, 86)',
        'rgb(75, 192, 192)',
        'rgb(54, 162, 235)',
        'rgb(153, 102, 255)',
        'rgb(201, 203, 207)'
      ];

    /**
     * @Route("/{_locale<%app.supported_locales%>}/user/app_error", name="app_error")
     */
    public function index(ErrorRepository $errorRepository, ErrorFamilyRepository $errorFamilyRepository, Request $request, PaginatorInterface $paginator, ChartBuilderInterface $chartBuilder): Response
    {
        
        $data = new SearchError();
        $data->page = $request->get('page', 1);
        $form = $this->createForm(SearchErrorType::class, $data);
        $form->handleRequest($request);
        
        $errors = $errorRepository->findSearch($data, $paginator);
        if($form->isSubmitted() && $form->isValid()) {
            if ($errors->getItems() == null) {
                $this->addFlash(
                    'app-error-alert', 'Error(s) not found, please try again !'
                );
                return $this->redirectToRoute('app_error');
            }
        }
        
        //count by errors
        $errorCount_array = $this->getDeviceCount($errorFamilyRepository);
        $errorChart = $this->getChart($chartBuilder, array_keys($errorCount_array), 'label', array_values($errorCount_array), 'Number of devices per error', Chart::TYPE_DOUGHNUT);

        $this->reportError($errorRepository);

        return $this->render('error/index.html.twig', [
            'errors' => $errors,
            'form' => $form->createView(),
            'errorChart' => $errorChart
        ]);
    }

    public function getDeviceCount(ErrorFamilyRepository $errorFamilyRepository) {
        $errorFamily = $errorFamilyRepository->findAll();
        for ($i=0; $i < sizeof($errorFamily); $i++) {
            $errorId = $errorFamily[$i]->getErrorId();
            $errorCount = count($errorFamily[$i]->getErrors());
            $deviceCountArray[$errorId] = $errorCount;
        }
        return $deviceCountArray;
    }

    /**
     * getChart
     * @param ChartBuilderInterface $chartBuilder
     * @param array $labels - array of legends
     * @param string $label
     * @param array $dataArray - array of values
     * @param string $text
     * @return Chart
     */
    function getChart(ChartBuilderInterface $chartBuilder, $labels, $label, $dataArray, $text, $chartType) {
        $backgroundArray = $this->backgroundArray;
        $borderArray = $this->borderArray;
        //Chart::TYPE_LINE
        $chart = $chartBuilder->createChart($chartType);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $label,
                    'backgroundColor' => $backgroundArray,
                    'borderColor' => $borderArray,
                    'data' => $dataArray,
                ],
            ],
        ]);
    
        $chart->setOptions([
            
            'plugins'=> [
                'title'=> [
                    'display'=> true,
                    'text'=> $text
                ]
            ],
            'maintainAspectRatio' => false,
        ]);

        return $chart;
    }

    /**
     * @Route("/update_db/{deviceType}", name="error_update")
     */
    function updateDb(KernelInterface $kernel, $deviceType) {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:errorCommand',
            // (optional) define the value of command arguments
            'deviceType' => $deviceType
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);
    }

    function reportError(ErrorRepository $errorRepository) {
        // check if error is 3
        // date of today
        $currentDate = date('Y-m-d');
        $date = date('2022-12-06');
        //var_dump($currentDate);
        $errorFamily = $errorRepository->findByDate($date);
        //var_dump($errorFamily);
        $result = [];
        $i = 0;
        foreach ($errorFamily as $key => $value) {
            
            $result[] = $value->getSn()->getSn();
        }
        $result2 = $this->countOccurences($result);
        var_dump($result2);

        // find all errors of today
        // if more than 3 records of sn are found for this date, add to result, to be send by mail
    }

    function countOccurences($inputArray) {
        $occurences = array_count_values($inputArray);
        return $occurences;
    }
}
