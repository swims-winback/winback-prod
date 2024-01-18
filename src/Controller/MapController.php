<?php

namespace App\Controller;

use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map')]
    public function index(DeviceRepository $deviceRepository): JsonResponse
    {
        $devices = $deviceRepository->findAll();
        $data = [];

        foreach ($devices as $device) {
            #$geoloc = $this->getLocationInfoByIp($device->getIpAddr());
            $data[] = [
                'sn' => $device->getSn(),
                'status' => $device->getIsActive(),
                'country' => $device->getCountry(),
                #'longitude' => $geoloc["longitude"],
                #'latitude' => $geoloc["latitude"]
            ];
        }
        return $this->json($data);
    }

    function getLocationInfoByIp($ip){
        $result = [];
        $result["longitude"] = "";
        $result["latitude"] = "";
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    
        if($ip_data && $ip_data->geoplugin_countryName != null){
            $result['longitude'] = $ip_data->geoplugin_longitude;
            $result['latitude'] = $ip_data->geoplugin_latitude;
        }
        return $result;
    }
}
