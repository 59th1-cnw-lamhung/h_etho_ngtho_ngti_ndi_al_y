<?php

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
    
    $mySQLStr = "SELECT distinct name_2, gid_1 from gadm36_vnm_2 where gid_1 = '{$_POST['gid_1']}'";

    $result = query($paPDO, $mySQLStr);
    
    if ($result != null)
    {
        $resFin = '<table>';
        // Lặp kết quả
        foreach ($result as $item){
            echo '<option value="'.$item["name_2"].'">'.$item["name_2"].'</option>';
        }
        $resFin = $resFin.'</table>';
        return $resFin;
        
    }
    else
        return ":((";
        
?>