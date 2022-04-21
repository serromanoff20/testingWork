<?php


namespace app\models;


class Geocoder
{
    /**
     * API-KEY getting with helping Yandex.Map
     */
    const API_KEY = '2fae61fe-3dab-49f7-9b08-31df5e33e588';
    const FILE = 'C:\Users\s.romanov\Desktop\Romanov_S\basic\repository\web\test_data_short_I';

    public $handle='';
    public $array_address;

    public function initFile()
    {
        $this->handle = fopen(self::FILE.'.csv', 'r');
        if (!$this->handle) {
            throw new Exception('Невозможно прочитать файл ' . self::FILE);
        }
        while(($data = fgetcsv($this->handle, 1000, ';')) !== false){
            $this->array_address[] = $data;
        }

        fclose($this->handle);
        return $this->decodeFile();
    }

    public function decodeFile(){
        $address_encode = false;
        $name_encode = false;

        foreach ($this->array_address as $fields){
            $address_encode = mb_detect_encoding($fields[2],'UTF-8,CP1251');
            $name_encode = mb_detect_encoding($fields[3],'UTF-8,CP1251');

            $arr_address_encode[] = iconv($address_encode, 'UTF-8', $fields[2]);
            $arr_name_encode[] = iconv($name_encode, 'UTF-8', $fields[3]);
        }
//        print_r($this->array_address);

        for ($i = 0; $i < count($arr_address_encode); $i++) {
            if (!(bool)$name_encode||!(bool)$address_encode){
                $this->array_address[$i][2] = $arr_address_encode[$i];
                $this->array_address[$i][3] = $arr_name_encode[$i];
            }
        }
//        print_r($this->array_address);

        return $this->Geocodering();
    }

    public function Geocodering(){
//        $this->initFile();
return $this->compare($this->array_address);//checking data
        foreach ($this->array_address as $item){
            set_time_limit(999);
            $ch = curl_init('https://geocode-maps.yandex.ru/1.x/?apikey='.self::API_KEY.'&format=json&geocode='.urlencode($item[2]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $res = curl_exec($ch);
            curl_close($ch);

            $tmpdata = json_decode($res, true);

            $coordinates[0] = $tmpdata['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
            $coordinates = explode(' ', $coordinates[0]);
            $oneData['name']=$item[2];
            $oneData['coordinates'] = $coordinates;
            $oneData['description'] = $item[3];
            $oneData['number'] = $item[4];
            $data[]=$oneData;
        }

        return $this->compare($data);
    }

    public function compare($data){
        $this->handle = fopen(self::FILE.'_new.csv', 'w');
//        for($i=0;$i<count($data);$i++){
//            if ($this->array_address[$i][1] != $data[$i]['coordinates'][0]){
//                if (!$this->handle) {
//                    throw new Exception('Невозможно прочитать файл ' . self::FILE);
//                }
//                $this->array_address[$i][0] = $data[$i]['coordinates'][1];
//                $this->array_address[$i][1] = $data[$i]['coordinates'][0];
//            }
//        }
//        $address_encode = mb_detect_encoding($data,'UTF-8,CP1251');
        //засовываем обратно с той же кодировкой
        for($i=0;$i<count($data);$i++){
            $address_encode[$i] = mb_detect_encoding($data[$i][2],'UTF-8');
            $name_encode[$i] = mb_detect_encoding($data[$i][3],'UTF-8');

            $address[$i] = mb_convert_encoding($data[$i][2], 'CP1251', $address_encode[$i]);
            $name[$i] = mb_convert_encoding($data[$i][3], 'CP1251', $name_encode[$i]);

            $data[$i][2] = $address[$i];
            $data[$i][3] = $name[$i];
        }
        foreach ($data as $fields){
            fputcsv($this->handle, $fields,';');
        }
        fclose($this->handle);
        return $this->array_address;
    }
}
