
<?php
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        //$paSRID = '4326';
        //$paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
        
        $aResult = "null";
        if ($functionname == 'getProvincialToAjax')
            $aResult = getProvincialToAjax($paPDO);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getDistrictToAjax($paPDO);
        
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
    
    
    function getProvincialToAjax($paPDO)
    {
        //echo $paPoint;
        //echo "<br>";
        //$paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_vnm_2\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        $mySQLStr = "SELECT distinct name_1 as name_1, gid_1 from gadm36_vnm_2";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        //echo $result;
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                
                echo '<option value="'.$item["gid_1"].'">'.$item["name_1"].'</option>';
            }
        }
        else
            return "null";
    }
    function getDistrictToAjax($paPDO)
    {
        //echo $paPoint;
        //echo "<br>";
        //$paPoint = str_replace(',', ' ', $paPoint);
        //echo $paPoint;
        //echo "<br>";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"cmr_adm1\" where ST_Within('SRID=4326;POINT(12 5)'::geometry,geom)";
        //$mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"gadm36_vnm_2\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        $mySQLStr = "SELECT * from gadm36_vnm_2 where gid_1 = ".$_POST["gid_1"];
        //id_1, shape_leng, 
		//echo $mySQLStr;
		
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                echo '<option value="'.$item["gid_1"].'">'.$item["name_2"].'</option>';
            }
            $resFin = $resFin.'</table>';
            return $resFin;
            
        }
        else
            return ":((";
    }
?>