<html>
    <body>
        <?php
            $imagen = "https://statics.laliga.es/img/sprite-escudos-2019-v1.png";

            $conn = new mysqli("localhost", "admin", "j14g07l95-Ax92z", "app_porras");
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT id_team, name, pix FROM team";
            $sql2 = "SELECT id_team, id_player, name, position FROM player";

            $result = $conn->query($sql);
            $result2 = $conn->query($sql2);

            while($row = $result->fetch_assoc()){
                echo '<h2>'.$row['name'].'</h2>';
                echo "
                <span style=\" 
                    display: block;
                    background-image: url(https://statics.laliga.es/img/sprite-escudos-2019-v1.png); 
                    background-position:".$row['pix']."; 
                    background-size: 40px 1720px; 
                    width: 40px; 
                    height: 40px;\">
                </span></br>";
                echo '<h4>Porteros</h4></br/>';
                while($row2 = $result2->fetch_assoc()){
                    if($row['id_team'] == $row2['id_team'] && $row2['position']==1){
                        echo $row2['name'];
                        echo '<img src="../img/'.$row2['id_player'].'.jpg" style="width: 40px; height: 40px">';
                        echo '<br/>';
                    }
                }
                echo '<br/><br/>';
                $result2 = $conn->query($sql2);
                echo '<h4>Defensas</h4></br/>';
                while($row2 = $result2->fetch_assoc()){
                    if($row['id_team'] == $row2['id_team'] && $row2['position']==2){
                        echo $row2['name'];
                        echo '<img src="../img/'.$row2['id_player'].'.jpg" style="width: 40px; height: 40px">';
                        echo '<br/>';
                    }
                }
                echo '<br/><br/>';
                $result2 = $conn->query($sql2);
                echo '<h4>Centrocampistas</h4></br/>';
                while($row2 = $result2->fetch_assoc()){
                    if($row['id_team'] == $row2['id_team'] && $row2['position']==3){
                        echo $row2['name'];
                        echo '<img src="../img/'.$row2['id_player'].'.jpg" style="width: 40px; height: 40px">';
                        echo '<br/>';
                    }
                }
                echo '<br/><br/>';
                $result2 = $conn->query($sql2);
                echo '<h4>Delanteros</h4></br/>';
                while($row2 = $result2->fetch_assoc()){
                    if($row['id_team'] == $row2['id_team'] && $row2['position']==4){
                        echo $row2['name'];
                        echo '<img src="../img/'.$row2['id_player'].'.jpg" style="width: 40px; height: 40px">';
                        echo '<br/>';
                    }
                }
                echo '<br/><br/><br/><br/><br/><br/>';
                $result2 = $conn->query($sql2);
            }
        ?>
    </body>
</html>