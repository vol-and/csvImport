<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

class User
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllUsers()
    {
        $stmt = $this->db->prepare('SELECT id, email, vorname, nachname, company, active, welcome_mail  FROM ' . TABLE_NAME );
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows >= 1) {
            $stmt->bind_result($id, $email, $vorname, $nachname, $company, $active, $welcome_mail);
            $o = [];
            $c = 0;

            while ($stmt->fetch()) {
                $o[$c]['id'] = $id;
                $o[$c]['email'] = $email;
                $o[$c]['vorname'] = $vorname;
                $o[$c]['nachname'] = $nachname;
                $o[$c]['company'] = $company;
                $o[$c]['active'] = $active;
                $o[$c]['welcome_mail'] = $welcome_mail;
                $c++;
            }

            return $o;
        } else {
            return false;
        }
    }

    public function getUserById($uid)
    {
        $stmt = $this->db->prepare('SELECT  email, vorname, nachname, company, active, welcome_mail, temp_hash  
                                    FROM ' . TABLE_NAME . ' WHERE id = ?');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($email, $vorname, $nachname, $company, $active, $welcome_mail, $temp_hash);
            $o = null;
            while ($stmt->fetch()) {
                $o['email'] = $email;
                $o['vorname'] = $vorname;
                $o['nachname'] = $nachname;
                $o['company'] = $company;
                $o['active'] = $active;
                $o['welcome_mail'] = $welcome_mail;
                $o['temp_hash'] = $temp_hash;
            }

            return $o;
        } else {
            return null;
        }
    }


    public function insertNewUsersCSV($post_data)
    {
        $newUser = 0;
        $count_rows = 1;
        $stmt = $this->db->prepare("INSERT INTO " . TABLE_NAME . " (email, vorname, nachname, company, temp_hash, import_time) VALUES (?,?,?,?,?,NOW()) ");

        array_shift($post_data);
        while ($user_data = array_shift($post_data)) {
            // 0 -email, 1 - nname, 2 - vname ; 3 - company;
            $count_rows++;
            $a = count($user_data);
            for ($i = 0; $i < $a; $i++) {
                $user_data[$i] = trim($user_data[$i]);
            }
            $email = strtolower($user_data[0]);
            $checkUser = $this->checkDuplicates($email);

            // if user doesn't exists - create one
            if ($checkUser['rows'] == 0) {
                $temp_hash = generateRandomString(64);
                $nachname = utf8_encode($user_data[1]);
                $vorname = utf8_encode($user_data[2]);
                $company = $user_data[3];
                $stmt->bind_param('sssss', $email, $vorname, $nachname, $company, $temp_hash);
                $stmt->execute();
                $newUser++;
            }
        }
        return $newUser;
    }

    public function checkDuplicates($email)
    {
        $stmt = $this->db->prepare('SELECT id FROM ' . TABLE_NAME . ' WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        $export_array = array();
        if ($stmt->num_rows >= 1) {
            $stmt->bind_result($uid);
            while ($stmt->fetch()) {
                $export_array['id'] = $uid;
            }
        }
        $export_array['rows'] = $stmt->num_rows;

        return $export_array;
    }

    public function toggleUserActivity($post)
    {
        $bool = $post['activity'] == 'activate' ? 1 : 0;
        $stmt = $this->db->prepare('UPDATE ' . TABLE_NAME . ' SET active = ?  WHERE id = ?');
        $stmt->bind_param('ii', $bool, $post['uid']);
        if($stmt->execute()) return true;

        return false;
    }

    public function deleteUser($post)
    {
        $stmt = $this->db->prepare('DELETE FROM ' . TABLE_NAME . ' WHERE id = ?');
        $stmt->bind_param('i', $post['uid']);
        if ($stmt->execute()) return true;

        return false;
    }

    public function updateWelcomeMailDate($uid)
    {
        $stmt = $this->db->prepare('UPDATE ' . TABLE_NAME . ' SET welcome_mail = NOW() WHERE id = ?');
        $stmt->bind_param('i',  $uid);
        if($stmt->execute()) return true;

        return false;
    }

    public function updateUserData($post)
    {
        $stmt = $this->db->prepare('UPDATE ' . TABLE_NAME . ' SET vorname = ?, nachname = ?, email = ?, company = ?
                                    WHERE id = ?');
        $stmt->bind_param('ssssi',  $post['vname'],  $post['nname'],  $post['email'],  $post['company'],  $post['uid'],);
        if($stmt->execute()) return true;

        return false;
    }

    private function slug($str)
    {
        $from = ["Á", "À", "Â", "Ä", "Ă", "Ā", "Ã", "Å", "Ą", "Æ", "Ć", "Ċ", "Ĉ", "Č", "Ç", "Ď", "Đ", "Ð", "É", "È", "Ė", "Ê", "Ë", "Ě", "Ē", "Ę", "Ə", "Ġ", "Ĝ", "Ğ", "Ģ", "á", "à", "â", "ä", "ă", "ā", "ã", "å", "ą", "æ", "ć", "ċ", "ĉ", "č", "ç", "ď", "đ", "ð", "é", "è", "ė", "ê", "ë", "ě", "ē", "ę", "ə", "ġ", "ĝ", "ğ", "ģ", "Ĥ", "Ħ", "I", "Í", "Ì", "İ", "Î", "Ï", "Ī", "Į", "Ĳ", "Ĵ", "Ķ", "Ļ", "Ł", "Ń", "Ň", "Ñ", "Ņ", "Ó", "Ò", "Ô", "Ö", "Õ", "Ő", "Ø", "Ơ", "Œ", "ĥ", "ħ", "ı", "í", "ì", "i", "î", "ï", "ī", "į", "ĳ", "ĵ", "ķ", "ļ", "ł", "ń", "ň", "ñ", "ņ", "ó", "ò", "ô", "ö", "õ", "ő", "ø", "ơ", "œ", "Ŕ", "Ř", "Ś", "Ŝ", "Š", "Ş", "Ť", "Ţ", "Þ", "Ú", "Ù", "Û", "Ü", "Ŭ", "Ū", "Ů", "Ų", "Ű", "Ư", "Ŵ", "Ý", "Ŷ", "Ÿ", "Ź", "Ż", "Ž", "ŕ", "ř", "ś", "ŝ", "š", "ş", "ß", "ť", "ţ", "þ", "ú", "ù", "û", "ü", "ŭ", "ū", "ů", "ų", "ű", "ư", "ŵ", "ý", "ŷ", "ÿ", "ź", "ż", "ž"];
        $to = ["A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "ss", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z"];

        $fromSig = [' '];
        $toSig = [''];

        $from = array_merge($from, $fromSig);
        $to = array_merge($to, $toSig);

        $newstr = str_replace($from, $to, $str);


        // echo $str.' >>> '.$newstr.'<br />';

        return strtolower($newstr);
    }
}