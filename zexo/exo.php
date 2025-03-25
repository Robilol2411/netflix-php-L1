<?php
//exo 1
$firstname = "Robin";
$lastname = "Matelot";
$age = 18;
$taille = 186;
$zipcode = 14000;
$ville = "Caen";
$rue = "42 rue des montagnes";
//exo 2
$int1 = -25;
$int2 = 123;
$float1 = 12.25;
$string1 ="hello 123";
//exo3
$bool1 =TRUE;
//exo4
$chiffre1 = 12;
$chiffre2 = 27;
$chiffre3 = 0;
//exo 5
$age1 = 18;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Projet PHP Simple</title>
    <link href="style.css" rel="stylesheet"></link>
</head>
<body>
    <!--
    <h1>Syntaxe</h1>

    <h2>Exercice 1:</h2>

    <h3 class="<?php echo $firstname; ?>">Bonjour , je m'appelle <?php echo $firstname; ?> <?php echo $lastname; ?> !</h3>
    <p>J'ai <?php echo $age; ?> ans, je fais <?php echo substr($taille,0,1); ?> m <?php echo substr($taille,-2); ?> et j'habite à <?php echo $ville; ?> dont le code postale est <?php echo $zipcode; ?></p>

    <h2>Exercice 2:</h2>

    <p>La valeur <?php echo $int1;?> en boolean est : <?php echo boolval($int1);?></p>
    <p>La valeur <?php echo $float1;?> en int est : <?php echo intval($float1);?></p>
    <p>La valeur <?php echo $string1;?> en int est : <?php echo intval($string1);?></p>
    <p>La valeur <?php echo $int2;?> en string est : <?php echo strval($int2);?></p>

    <h2>Exercice 3:</h2>

    <p>La valeur <?php echo $int1;?> en boolean est : <?php echo boolval($int1);?></p>
    <p>La valeur <?php echo $bool1;?> en number est : <?php echo intval($float1);?></p>
    <p>La valeur <?php echo $string1;?> en boolean est : <?php echo boolval($string1);?></p>

    <h1>Mathematical operations</h1>

    <h2>Exercice 1:</h2>

    <?php function add($chiffre1,$chiffre2){
        return $chiffre1 + $chiffre2;
    }; ?>

    <?php function substract($chiffre1,$chiffre2){
        return $chiffre1 - $chiffre2;
    }; ?>

    <?php function divide($chiffre1,$chiffre2){
        return $chiffre1 / $chiffre2;
    }; ?>

    <?php function multiply($chiffre1,$chiffre2){
        return $chiffre1 * $chiffre2;
    }; ?>

    <p> <?php echo $chiffre1;?> + <?php echo $chiffre2;?> = <?php echo add($chiffre1,$chiffre2);?> </p>
    <p> <?php echo $chiffre2;?> - <?php echo $chiffre1;?> = <?php echo substract($chiffre2,$chiffre1);?> </p>
    <p> <?php echo $chiffre2;?> / <?php echo $chiffre1;?> = <?php echo divide($chiffre2,$chiffre1);?> </p>
    <p> <?php echo $chiffre2;?> * <?php echo $chiffre1;?> = <?php echo multiply($chiffre1,$chiffre2);?> </p>

    <h2>Exercice 2:</h2>

    <p> Pi vaut : <?php echo pi(); ?> <p>

    <h1>Conditions</h1>

    <h2>Exercice 1:</h2>

    <p>Vous avez <?php echo $age1; ?> ans, vous <?php if ($age1 > 18 or $age1 == 18) echo "pouvez accéder à cette page";?> <?php if($age1 < 18) echo "ne pouvez pas accéder à la page"; ?></p>

    </p>

    <h2>Exercice 2 :</h2>

    <p>
        <?php $note = 4;
        if ($note > 10 or $note == 10) {
            echo 'Vous avez la moyenne avec une note de ';
            echo $note;
        }
        else {
            echo 'Vous n avez pas la moyenne avec une note de ';
            echo $note;
        }
        ?>
    </p>

    <h2>Exercice 3 :</h2>

    <p> 
            <?php $grade = 45; ?>
            <?php if ($grade > 0 && $grade < 9){?> <p>...</p> <?php } 
            elseif ($grade > 10 && $grade < 20){?> <p>catastrophique</p> <?php } 
            elseif ($grade > 20 && $grade < 30){?> <p>à chier</p> <?php }
            elseif ($grade > 30 && $grade < 40){?> <p>nul</p> <?php }
            elseif ($grade > 40 && $grade < 50){?> <p>pas ouf</p> <?php }
            elseif ($grade > 50 && $grade < 60){?> <p>ok</p> <?php }
            elseif ($grade > 60 && $grade < 70){?> <p>normal</p> <?php }
            elseif ($grade > 70 && $grade < 80){?> <p>bien</p> <?php }
            elseif ($grade > 80 && $grade < 90){?> <p>très bien</p> <?php }
            elseif ($grade > 90 && $grade < 100){?> <p>parfait</p> <?php }
            else {?> <p> pas possible </p> <?php }?>
    </p> 

    </br>

    <h1>Jour 2</h1>

    <h1>Loops</h1>

    <h2>exo 1</h2>

    <?php 
    $x=5;
    for ($i=1;$i<$x;$i++) {
        if ($i%2 == 0) { ?> <p> Le chiffre <?php echo $i; ?> est pair</p> <?php }
        else {?> <p> Le chiffre <?php echo $i; ?> est impair </p> <?php } 
    } ?>
  
    <h1>Arrays</h1>
    <h2>exo 1</h2>
    <?php 
    $userInfo = [
        "firstname" => "robin",
        "lastname" => "matelot",
        "age"=> "18",
        $adresse = [
            "zipcode"=> 14000,
            "ville"=> "caen",
            "rue"=> "42 rue des montagnes"
        ]];?>

    <p>Je m'appelle <?php echo $userInfo["firstname"]; ?> <?php echo $userInfo["lastname"]; ?>, j'ai <?php echo $userInfo["age"]; ?> ans et j'habite au <?php echo $adresse["rue"]; ?> <?php echo $adresse["ville"]; ?> <?php echo $adresse["zipcode"]; ?>.</p>

    <h2>exo 2</h2>

    <?php 
    $userSelect = 3;
    $userInfo2 = [["id"=> 1,"firstname" => "robin","lastname" => "matelot","age"=> "18","adresse" => ["zipcode"=> 14000,"ville"=> "caen","rue"=> "42 rue des montagnes"]],
                ["id"=> 2,"firstname" => "noé","lastname" => "le bg","age"=> "18","adresse" => ["zipcode"=> 50622,"ville"=> "bayeux","rue"=> "22 rue de la tarte aux pommes"]],
                ["id"=> 3,"firstname" => "eliott","lastname" => "psss","age"=> "20","adresse" => ["zipcode"=> 58952,"ville"=> "cherbourg","rue"=> "22 rue de la tarte aux poires"]],
                ["id"=> 4,"firstname" => "leo","lastname" => "le rigolo","age"=> "19","adresse" => ["zipcode"=> 87985,"ville"=> "octeville","rue"=> "22 rue de la tarte aux fraise"]],
                ["id"=> 5,"firstname" => "baptiste","lastname" => "le fou du bus","age"=> "16","adresse" => ["zipcode"=> 21654,"ville"=> "paris","rue"=> "22 rue de la tarte aux citron"]],
                ["id"=> 6,"firstname" => "pierre","lastname" => "le violeur","age"=> "17","adresse" => ["zipcode"=> 49452,"ville"=> "boulogne","rue"=> "22 rue de la tarte aux abricot"]],
                ["id"=> 7,"firstname" => "titouane","lastname" => "le retardataire","age"=> "21","adresse" => ["zipcode"=> 98563,"ville"=> "lyon","rue"=> "22 rue de la tarte aux oranges"]],
                ["id"=> 8,"firstname" => NULL ,"lastname" => "le deuxième bg","age"=> "22","adresse" => ["zipcode"=> 42354,"ville"=> "marseille","rue"=> "22 rue de la tarte aux chocolats"]],
                ["id"=> 9,"firstname" => "Louis","lastname" => "le mec addicte","age"=> "34","adresse" => ["zipcode"=> 79845,"ville"=> "cacaland","rue"=> "22 rue de la tarte aux framboise"]],
                ["id"=> 10,"firstname" => "Favé","lastname" => "le tchoufareur","age"=> "27","adresse" => ["zipcode"=> 16135,"ville"=> "le mans","rue"=> "22 rue de la tarte aux caca"]]];?>
    <?php for ($i=0;$i<count($userInfo2);$i++) {
        if ($userInfo2[$i]["id"] == $userSelect){ ?>
            <p>Je m'appelle <?php echo $userInfo2[$i]["firstname"]; ?> <?php echo $userInfo2[$i]["lastname"]; ?>, j'ai <?php echo $userInfo2[$i]["age"]; ?> ans et j'habite au <?php echo $userInfo2[$i]["adresse"]["rue"]; ?> <?php echo $userInfo2[$i]["adresse"]["ville"]; ?> <?php echo $userInfo2[$i]["adresse"]["zipcode"]; ?>.</p> <?php
        }
    }; ?>

    <h2>exo 3</h2>

    <?php for ($i=0;$i<count($userInfo2);$i++) {
        if ($userInfo2[$i]["firstname"] == NULL ){ ?>
            <p> L'id <?php echo $userInfo2[$i]["id"]; ?> n'a pas de firstname</p> <?php }
        elseif ($userInfo2[$i]["lastname"] == NULL ){ ?>
            <p> L'id <?php echo $userInfo2[$i]["id"]; ?> n'a pas de lastname</p> <?php }
        elseif ($userInfo2[$i]["age"] == NULL ){ ?>
            <p> L'id <?php echo $userInfo2[$i]["id"]; ?> n'a pas d'âge</p> <?php }
         }; ?>

    <h1>function</h1>

    <h2>exo 1 </h2>

    <?php function factoriel($chiffre,$puissance){
        return ($chiffre)**$puissance;
        } 
        $chiffre = 3;
        $puissance = 2;?>
    <p> <?php echo $chiffre; ?> puissance <?php echo $puissance; ?> est égal à <?php echo factoriel($chiffre,$puissance); ?> </p>
    
    <h2>exo 2</h2>

    <?php function fibonacci($n){
        if ($n <=1){
            return $n;
        }
        return fibonacci($n-1) + fibonacci($n-2);
    }?>

    <p> La suite de Fibonacci est <?php for ($i=0;$i<10;$i++){echo fibonacci($i)," ";} ?> </p>

    <h2>exo 3</h2>

    <?php 
    $res="";
    $mots =["cou","cou"];
    function concat($mots,$res){
        for ($i=0;$i<count($mots);$i++){
            $res=$res.$mots[$i];
        }
        return $res;
    } ?>
    <p> <?php echo $mots[0]; ?> et <?php echo $mots[1]; ?> donnent <?php echo concat($mots,$res); ?></p>

    <h2>exo 4</h2>

    <?php 
    $texte="ed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam";
    $lettre="e";
    $x=0;
    function recherche($texte,$lettre,$x){
        for ($i=0;$i<strlen($texte);$i++){
            if ($texte[$i]==$lettre){
                $x=$x+1;
            }
        }
        return $x;
    } ?>

    <p> Dans le texte : <?php echo $texte; ?> | Il y a <?php echo recherche($texte,$lettre,$x); ?> fois la lettre <?php echo $lettre; ?> </p>
    
    <h2>Jour 3</h2>

    <h2>FizzBuzz</h2>
    <div class="form-container">
        <form id="contact-form" action="index.php" method="post">
            <div class="form-group">
                Numero de depart: <input type="text" id="name" class="form-input" name="depart" required><br>
            </div>
            <div class="form-group">
                Numero de fin: <input type="text" id="name" class="form-input" name="fin" required><br>
            </div>
            <button type="submit" class="submit-button">Envoyer</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['depart']) && isset($_POST['fin'])) {
        $depart = (int)$_POST['depart'];
        $fin = (int)$_POST['fin'];
        
        if ($depart <= 0) {
            echo "Numéro de début ne peut pas être inférieur ou égal à 0";
        } else {
            echo "<p>Numéro de début : $depart</p>";
            echo "<p>Numéro de fin : $fin</p>";
            
            for ($i = $depart; $i <= $fin; $i++) {
                if ($i % 3 == 0 && $i % 5 == 0) {
                    echo "<p>$i : FizzBuzz</p>";
                } elseif ($i % 3 == 0) {
                    echo "<p>$i : Fizz</p>";
                } elseif ($i % 5 == 0) {
                    echo "<p>$i : Buzz</p>";
                }
            }
        }
    }
    ?>

    <h2>Calculatrice</h2>
    <div class="form-container">
        <form id="contact-form" action="index.php" method="post">
            <div class="form-group">
                Numero 1: <input type="number" id="name" class="form-input" name="n1" required><br>
            </div>
            <div class="form-group">
                Numero 2: <input type="number" id="name" class="form-input" name="n2" required><br>
            </div>
            <div class="form-group">
                Numero 3: <input type="number" id="name" class="form-input" name="n3"><br>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="option1" name="options[]" value="addition">
                <label for="option1" class="checkbox-label">
                <span class="checkbox-custom"></span>
                Addition
                </label>    
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="option2" name="options[]" value="soustraction">
                <label for="option2" class="checkbox-label">
                    <span class="checkbox-custom"></span>
                    Soustraction
                </label>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="option3" name="options[]" value="multiplication">
                <label for="option3" class="checkbox-label">
                <span class="checkbox-custom"></span>
                    Multiplication
                </label> 
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="option4" name="options[]" value="division">
                <label for="option4" class="checkbox-label">
                <span class="checkbox-custom"></span>
                    Division
                </label> 
            </div>
            <button type="submit" class="submit-button">Envoyer</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['n1']) && isset($_POST['n2']) && isset($_POST['n3']) && isset($_POST['options'])) {
        $n1 = (float)$_POST['n1'];
        $n2 = (float)$_POST['n2'];
        $n3 = (float)$_POST['n3'];
        $options = $_POST['options'];

        foreach ($options as $option) {
            switch ($option) {
                case "addition":
                    echo "<p>Addition: " . ($n1 + $n2 + $n3) . "</p>";
                    break;
                case "soustraction":
                    echo "<p>Soustraction: " . ($n1 - $n2 - $n3) . "</p>";
                    break;
                case "multiplication":
                    echo "<p>Multiplication: " . ($n1 * $n2 * $n3) . "</p>";
                    break;
                case "division":
                    if ($n2 == 0 || $n3 == 0) {
                        echo "<p>Erreur : Division par zéro non autorisée.</p>";
                    } else {
                        echo "<p>Division: " . ($n1 / $n2 ) . "</p>";
                    }
                    break;
            }
        }
    }
    ?>
    <form action="index.php" method="post"> 
        phrase : <textarea name="caesar" rows="1" cols="50"></textarea><br>
        <br> 
        <input type="submit">
    </form>
    <?php
    function caesarCipher($message, $key, $encrypt = true) {
        if (!$encrypt) {
            $key = -$key;
        }

        $key = $key % 26;
        if ($key < 0) {
            $key += 26;
        }

        $result = '';
        $length = strlen($message);

        for ($i = 0; $i < $length; $i++) {
            $char = $message[$i];

            if (ctype_alpha($char)) {
                $isUpper = ctype_upper($char);

                $ascii = ord($char);

                if ($isUpper) {
                    $ascii = (($ascii - 65 + $key) % 26) + 65;
                } else {
                    $ascii = (($ascii - 97 + $key) % 26) + 97;
                }

                $char = chr($ascii);
            }

            $result .= $char;
        }

        return $result;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $originalMessage = htmlspecialchars($_POST['caesar']);
    }
    $encryptionKey = 3;
    $encryptedMessage = caesarCipher($originalMessage, $encryptionKey);
    echo $encryptedMessage . "\n";?>
    <h2>securité</h2>
    <form id="contact-form" action="index.php" method="post">
            <div class="form-group">
                Nom: <input type="text" id="name" class="form-input" name="Nom" ><br>
            </div>
            <div class="form-group">
                Code postale: <input type="number" id="adresse" class="form-input" name="Codepostale" ><br>
            </div>
            <div class="form-group">
                Ville: <input type="text" id="adresse" class="form-input" name="Ville" ><br>
            </div>
            <div class="form-group">
                Phone number: <input type="text" id="number" class="form-input" name="Numéro" ><br>
            </div>
            <div class="form-group">
                E-mail: <input type="email" id="email" class="form-input" name="Mail" ><br>
            </div>
            <button type="submit" class="submit-button">Envoyer</button>
    </form>
    <?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Nom"])) {
        echo $nameErr = "Name est requis";
    } else {
        $name = test_input($_POST["Nom"]);
        if (!preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/u", $name)) {
            echo $nameErr = "Uniquement lettre latin";
        }
    }
    
    if (empty($_POST["Codepostale"])) {
        echo $postalCodeErr = "Code postale requis";
    } else {
        $postalCode = test_input($_POST["Codepostale"]);
        if (!preg_match("/^[0-9]{5}$/", $postalCode)) {
            echo $postalCodeErr = "Doit faire 5 caractères";
        }
    }
    
    if (empty($_POST["Ville"])) {
        echo $cityErr = "Nom de ville requis";
    } else {
        $city = test_input($_POST["Ville"]);
        if (!preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/u", $city)) {
            echo $cityErr = "Uniquement lettre latin";
        }
    }
    
    if (empty($_POST["Numéro"])) {
        echo $phoneErr = "Numéro requis";
    } else {
        $phone = test_input($_POST["Numéro"]);
        $cleanPhone = preg_replace('/\s+/', '', $phone);
        if (!preg_match("/^(\+33|0)[1-9][0-9]{8}$/", $cleanPhone)) {
            echo $phoneErr = "Format de numéro invalide. Utilisez le format +33XXXXXXXXX ou 0XXXXXXXXX ";
        }
    }

    if (empty($_POST["Mail"])) {
        echo $emailErr = "Email requis";
    } else {
        $email = test_input($_POST["Mail"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo $emailErr = "Format d'email invalide";
        }
    }

    if (empty($nameErr) && empty($addressErr) && empty($phoneErr) && empty($emailErr) && empty($postalCodeErr) && empty($cityErr)) {
        $formSuccess = true;
    }
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    ?>

    <form id="contact-form" action="index.php" method="post">
            <div class="form-group">
                Login: <input type="text" id="name" class="form-input" name="login" ><br>
            </div>
            <div class="form-group">
                Password: <input type="text" id="adresse" class="form-input" name="password" ><br>
            </div>
            <button type="submit" class="submit-button">Envoyer</button>
    </form>
    <?php 
        $login1 = "Robilol2411";
        $password1 = '$2y$10$UKd2F9ui.ntYbaczSOI43OMyJrpIrWHCEJDVYyu4rj0BJJ.6/jkbu';
        $erreur = 0;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["login"])) {
                echo $loginErr = "Login est requis";
            } else {
                $login = test_input($_POST["login"]);
                if ($login != $login1){
                    $erreur = $erreur + 1;
                }
            }

            if (empty($_POST["password"])) {
                echo $passwordErr = " Password requis";
            } else {
                $password = test_input($_POST["password"]);
                $passwordv = password_verify($password,$password1);
                if ($passwordv!= $password1){
                    $erreur = $erreur + 1;
                }
            }
            if ($erreur != 0){
                echo "Login ou mot de passe incorrect";
            }

            if (empty($loginErr) && empty($passwordErr)) {
                $formSuccess = true;
            }
            };?>
    <form action="index.php" method="post" enctype="multipart/form-data">
    Sélectionnez un fichier à téléverser :
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload file" name="submit">
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "uploads/";

    // Assurer l'existence du dossier d'upload
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérification de l'extension du fichier
    if ($fileType != "txt") {
        echo "Erreur : Seuls les fichiers .txt sont autorisés.";
        $uploadOk = 0;
    }

    // Vérification du type MIME
    $mimeType = mime_content_type($_FILES["fileToUpload"]["tmp_name"]);
    if ($mimeType !== "text/plain") {
        echo "Erreur : Type de fichier non valide.";
        $uploadOk = 0;
    }

    if ($uploadOk && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "Le fichier " . basename($_FILES["fileToUpload"]["name"]) . " a été téléversé.<br>";

        // Initialisation des compteurs de lettres
        $letter_counts = array_fill_keys(range('a', 'z'), 0);

        // Ouverture du fichier pour lecture
        $file = fopen($target_file, "r");

        if ($file) {
            while (($line = fgets($file)) !== false) {
                foreach (str_split(strtolower($line)) as $char) {
                    if (ctype_alpha($char)) {
                        $letter_counts[$char]++;
                    }
                }
            }
            fclose($file);

            // Affichage des occurrences des lettres
            foreach ($letter_counts as $letter => $count) {
                echo "$letter: $count <br>";
            }

            // Taille du fichier
            echo "Taille du fichier : " . filesize($target_file) . " octets";
        } else {
            echo "Erreur : impossible d'ouvrir le fichier.";
        }
    } else {
        echo "Erreur lors du téléversement du fichier.";
    }
}
?>
<?php
$logFile = "logs.txt";
$date = date("Y-m-d H:i:s");
$headers = getallheaders();
$headersJson = json_encode($headers, JSON_PRETTY_PRINT);
$method = $_SERVER['REQUEST_METHOD'];
$body = file_get_contents("php://input");
$logEntry = "----------------------\n";
$logEntry .= "Date: $date\n";
$logEntry .= "Method: $method\n";
$logEntry .= "Headers:\n$headersJson\n";
$logEntry .= "Body:\n$body\n";
$logEntry .= "----------------------\n\n";
$file = fopen($logFile, "a");
if ($file) {
    fwrite($file, $logEntry);
    fclose($file);
} else {
    error_log("Impossible d'ouvrire le fichier.");
}
?>
-->
<h2>Liste des Utilisateurs</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Pseudo</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['description']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
        $searchResult = null;
        $searchPerformed = false;
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = trim($_GET['search']);
            $searchPerformed = true;
            try {
                $stmt = $conn->prepare("SELECT id, email, username, description FROM user WHERE email LIKE ? OR username LIKE ?");
                $stmt->execute(["%$search%", "%$search%"]);
                $searchResult = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $errorMessage = "Erreur lors de la recherche: " . $e->getMessage();
            }
        }
        
        // Récupération de tous les utilisateurs
        $users = [];
        try {
            $users = $conn->query('SELECT id, email, username, description FROM user')->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de la récupération des utilisateurs: " . $e->getMessage();
        }
    ?>
    <div class="info-box">
        <h2>Rechercher un utilisateur</h2>
        <div class="center2">
            <form method="GET" action="" class="search-form">
            <span class="input-span , form">
                <input type="text" name="search" placeholder="Email ou username" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <input class="submit" type="submit" value="Rechercher">
            </span>
            </form>
        </div>
        
        <?php if ($searchPerformed): ?>
            <?php if ($searchResult): ?>
                <div class="user-detail">
                    <h3>Résultat de la recherche</h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($searchResult['id']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($searchResult['email']); ?></p>
                    <p><strong>username:</strong> <?php echo htmlspecialchars($searchResult['username']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($searchResult['description']); ?></p>
                </div>
            <?php else: ?>
                <p>Aucun utilisateur trouvé avec ces critères.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>