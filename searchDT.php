
<?php
    
    //include ('searchDT.php');

    $paPDO = new PDO('pgsql:host=localhost;dbname=VietNam_GADM;port=5432', 'postgres', '140599');

    
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
    // Diện tích
    $mySQLStr = "SELECT name_2 from gadm36_vnm_2 where gid_1 = '{$_POST['gid_1']}' AND ST_Area(geom::geography)/1000000 < '{$_POST['dt']}'";
    // Chu vi
    //$mySQLStr = "SELECT name_2 from gadm36_vnm_2 where gid_1 = '{$_POST['gid_1']}' AND ST_Perimeter(geom::geography)/1000 < '{$_POST['dt']}'";

    $result = query($paPDO, $mySQLStr);

    if ($result != null)
        {
            //$resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                echo '<option value="'.$item["name_2"].'">'.$item["name_2"].'</option>';
            }
            //$resFin = $resFin.'</table>';
            return $resFin;
            
        }
        else
            return ":((";


    /*if ($result != null)
        {
            $resFin = '<table class="table table-bordered">';
            // Lặp kết quả
            foreach ($result as $item){
                
                $resFin = $resFin.'<tr><td>Quận (Huyện): '.$item['name_2'].'</td></tr>';
                
               
            }
            $resFin = $resFin.'</table>';
            echo $resFin;
            
        }
        else
            return null;*/
        
?>
