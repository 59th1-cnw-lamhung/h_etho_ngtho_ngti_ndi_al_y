
<?php
  // Kết nối CSDL
        // $paPDO = new PDO('pgsql:host=localhost;dbname=BTL;port=5432', 'postgres', '696969');
        include 'connectDB.php';
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

       $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from gadm36_vnm_2 where name_2 = '{$_POST['name2']}'";

        $result = query($paPDO, $mySQLStr);

        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
            }
        }
        else
            return "null";
    

?>