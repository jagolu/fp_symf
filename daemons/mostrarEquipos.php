<html>
    <body>
        <?php
            $imagen = "https://statics.laliga.es/img/sprite-escudos-2019-v1.png";

            $conn = new mysqli("localhost", "admin", "j14g07l95-Ax92z", "app_porras");
            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            $sql = "SELECT nombre, pixeles FROM equipos";
            $result = $conn->query($sql);

            while($row = $result->fetch_assoc()){
                echo $row['nombre'];
                echo "
                <span style=\" 
                    display: block;
                    background-image: url(https://statics.laliga.es/img/sprite-escudos-2019-v1.png); 
                    background-position:".$row['pixeles']."; 
                    background-size: 40px 1720px; 
                    width: 40px; 
                    height: 40px;\">
                </span></br>";
            }
        ?>
    </body>
</html>