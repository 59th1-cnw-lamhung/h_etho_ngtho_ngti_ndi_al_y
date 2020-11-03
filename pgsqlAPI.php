
<?php
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
            $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);

        
        echo $aResult;
    
        closeDB($paPDO);
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=VietNam_GADM;port=5432', 'postgres', '140599');
        return $paPDO;
    }
    
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;     
                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }
    
    
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_vnm_2\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from gadm36_vnm_2 where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        //echo $result;
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cmr_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_vnm_2\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $mySQLStr = "SELECT ST_Area(geom::geography)/1000000 as area, danso.name_2 as name2, danso.name_1 as name1, gid, ST_Perimeter(geom::geography)/1000 as peri, sodan, matdo_danso from gadm36_vnm_2, danso where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom) and gadm36_vnm_2.name_2=danso.name_2 and gadm36_vnm_2.name_1=danso.name_1";
        //id_1, shape_leng, 
		//echo $mySQLStr;
		
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>id: '.$item['gid'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Tỉnh(thành phố): '.$item['name1'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Quận(huyện): '.$item['name2'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Chu vi: '.round($item['peri'],3). ' km </td></tr>';
                $resFin = $resFin.'<tr><td>Diện tích: '.round($item['area'],3).' km<sup>2</sup></td></tr>';
                $resFin = $resFin.'<tr><td>Dân số: '.$item['sodan'].' người</td></tr>';
                $resFin = $resFin.'<tr><td>Mật độ dân số: '.$item['matdo_danso'].' người/km<sup>2</sup></td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
            
        }
        else
            return "";
    }


?>