<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='style.css'>
    <link href='https://fonts.googleapis.com/css2?family=Montserrat&display=swap' rel='stylesheet'>
    <title>Age tester</title>
</head>

<body>
    <?php
    date_default_timezone_set('Europe/Vilnius');

    $userDateArr = array();
    $userDate    = '';
    $userAge     = '';
    $errors      = array();
    $confirm     = '';

    // laiko ribojimai
    $time10      = '10:00';
    $time20      = '20:00';
    $time15      = '15:00';

    // css klasių keitimas
    $msgClass    = '';
    $NoteClass   = '';
    $blockClass  = 'hide';

    // svaites dienos lietuviskai
    $weekLt = array(
        '0' => 'Sekmadienis',
        '1' => 'Pirmadienis',
        '2' => 'Antradienis',
        '3' => 'Trečiadienis',
        '4' => 'Ketvirtadienis',
        '5' => 'Penktadienis',
        '6' => 'Šeštadienis'
    );

    // Specialios datos
    $specialDate = array(
        '0' => '09-01',
        '1' => '11-05'
    );

    // Savaites dienos lt
    foreach ($weekLt as $key => $day) {
        if (date('w') == $key) {
            $weekDay = $day;
        }
    }

    // Datos tikrinimas
    foreach ($specialDate as $day) {
        if ($day == date('m-d')) {
            $message = $day . '<br>Alkoholio pardavimas<br>DRAUDŽIAMAS';
            $msgClass = 'red';
        } else {
            if ((date('H:i') < $time10) || (date('H:i') > $time20)) {
                $message = 'DRAUDŽIAMAS';
                $msgClass = 'red';
            } elseif ((date('w') == 0) && ((date('H:i') < $time10) || (date('H:i') > $time15))) {
                $message = 'DRAUDŽIAMAS';
                $msgClass = 'red';
            } else {
                $messageYes = 'LEIDŽIAMAS';
                $msgClass = 'green';
            }
        }
    }

    // Ivestos datos tikrinimas
    if (!empty($_POST)) {
        $userDate    = trim($_POST['date']);
        $userDateArr = explode('-', $userDate);

        $Year  = isset($userDateArr[0]) ?  $userDateArr[0] : '';
        $Month = isset($userDateArr[1]) ?  $userDateArr[1] : '';
        $Day   = isset($userDateArr[2]) ?  $userDateArr[2] : '';

        if (empty($userDate)) {
            $errors['date'] = 'Data neįvesta';
        } elseif (!checkdate((int) $Month, (int) $Day, (int) $Year)) {
            $errors['date'] = 'Patikrinkite datos formatą...';
        }
        if (empty($errors)) {
            $confirm = 'Datos formatas tinkamas';
        }
    }

    // amžiaus paskaičiavimas tikrinimas
    if (!empty($errors)) {
    } else {
        if (!empty($userDate)) {
            //$userAge = floor((time() - strtotime($userDate)) / 31556926);
            $userAge = intval(date('Y', time() - strtotime($userDate))) - 1970;
            $blockClass  = '';
            if ($userAge < 0) {
                $message2 = 'Šis pirkėjas dar negimė :)';
                $NoteClass = 'red';
            } elseif ($userAge > 100) {
                $message2 = 'Ar pirkėjui tikrai tiek daug metų :)';
                $NoteClass = 'red';
            } elseif ($userAge < 20) {
                $message2 = 'Parduoti NEGALIMA';
                $Adult = $Year + 20;
                $future = strtotime("$Adult-$Month-$Day");
                $timestamp = $future - time();
                $moreInfo = 'Pasiūlimas ateiti po  ' . floor($timestamp / 31556926) . ' metų ' . ceil(($timestamp % 31556926) / 86400) . ' dienų.';
                $NoteClass = 'red';
            } else {
                $message2 = 'Parduoti GALIMA';
                $NoteClass = 'green';
                if (empty($message)) {
                    $moreInfo = 'Nusimato Balius :)';
                } elseif (!empty($message)) {
                    $moreInfo = 'Bet laikas netinkamas';
                    // $timestamp =(strtotime('tomorrow')+36000)-time();
                    // $moreInfo2 = 'Pasiūlimas ateiti po  '. floor($timestamp / 3600).' val. '. ceil(($timestamp % 3600)/ 60) .' min.';
                }
            }
        }
    }

    ?>

    <div class='container'>
        <div class='blocks'>
            <div class='block'>
                <h3 class='title'>Gimimo datos tikrinimas</h3>
                <form class='form' method='post'>
                    <label for='date'></label><br>
                    <span class='error'><?php echo isset($errors['date']) ?  $errors['date'] : ''; ?></span>
                    <span class='confirm'><?php echo !empty($confirm) ?  $confirm : ''; ?></span>

                    <input type='text' name='date' value='<?= $userDate; ?>' placeholder='YYYY-MM-DD'>
                    <span class='info'>Gimimo datos formatas: YYYY-MM-DD</span>
                    <input type='submit' value='Tikrinti' name='Tikrinti'></input>
                </form>
            </div>
            <div class='block1'>
                <h3 class='title'>Šiandiena</h3>
                <h1 class='title'><?= date('Y-m-d'); ?></h1>
                <h1 class='title'><?= $weekDay; ?></h1>
            </div>
            <div class='block'>
                <div class='<?= $blockClass; ?>'>
                    <p class='message'>Pirkėjo amžius: <?php echo !empty($userAge) ? $userAge : ''; ?> m. </p>
                    <p class='message <?= $NoteClass; ?>'><?php echo !empty($message2) ? $message2 : ''; ?></p>
                    <p class='message'><?php echo !empty($moreInfo) ? $moreInfo : ''; ?></p>
                    <p class='message green'><?php echo !empty($moreInfo2) ? $moreInfo2 : ''; ?></p>
                </div>
            </div>
            <div class='block1'>
                <p class='message'>Alkoholio pardavimas <br> šiuo metu: </p>
                <p class='message <?= $msgClass; ?>'><?php echo !empty($message) ? $message : $messageYes; ?></p>
            </div>
        </div>
        <footer>
            <p><?= date('Y'); ?> m. &lt; sim / zim &gt;</p>
        </footer>
    </div>

</body>