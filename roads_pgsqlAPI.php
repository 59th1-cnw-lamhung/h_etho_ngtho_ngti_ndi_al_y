<?php

    function initDB()
    {
        // Kết nối CSDL
        // $paPDO = new PDO('pgsql:host=localhost;dbname=BTL;port=5432', 'postgres', '696969');
        include 'connectDB.php';
        return $paPDO;
        
    }
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getGeoEagleToAjax')
            $aResult = getGeoEagleToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoEagleToAjax')
            $aResult = getInfoEagleToAjax($paPDO, $paSRID, $paPoint);
        
        echo $aResult;
    
        closeDB($paPDO);
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
    function getGeoEagleToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        $strDistance = "ST_Distance('".$paPoint."',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('".$paPoint."',ST_AsText(geom))) from vnm_roads";
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from vnm_roads where ".$strDistance." = (".$strMinDistance.") and ".$strDistance." < 0.05";
        //$mySQLStr = "SELECT * FROM vnm_roads";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
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
    function getInfoEagleToAjax($paPDO,$paSRID,$paPoint)
    {
        //echo $paPoint;
        //echo "<br>";
        $paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        $strDistance = "ST_Distance('".$paPoint."',ST_AsText(geom))";
        $strMinDistance = "SELECT min(ST_Distance('".$paPoint."',ST_AsText(geom))) from vnm_roads";
        $mySQLStr = "SELECT ST_Length(geom::geography)/1000 as length1, f_code_des from vnm_roads where ".$strDistance." = (".$strMinDistance.") and ".$strDistance." < 0.05";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td>Độ dài: '.round($item['length1'],3).'km</td></tr>';
                $resFin = $resFin.'<tr><td>Loại đường: '.($item['f_code_des']=="Road"?"Đường chính":"Đường mòn").'</td></tr>';
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "null";
    }
?>