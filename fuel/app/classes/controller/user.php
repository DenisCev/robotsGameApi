<?php 
class Controller_User extends Controller_Base
{
    public function post_create()
    {
    	try
    	{
    		if(!isset($_POST['name']) || 
                !isset($_POST['pass']) || 
                !isset($_POST['email']) ||
                !isset($_POST['victories']) ||
                !isset($_POST['defeats'])) 
            {
                return self::EmptyError();
            }

            $input = $_POST;
          
            if(empty($input['name']) || strlen($input['name']) < 4)
            {
                $response = $this->response(array(
                    'code' => 400,
                    'message' => 'El nombre debe de tener almenos 4 caracteres',
                    'data' => ''
                ));
                return $response;
            }

            $usersName = Model_Users::find('all', array(
                'where' => array(
                    array('name', $input['name'])
                )
            ));

            $usersEmail = Model_Users::find('all', array(
                'where' => array(
                    array('email', $input['email'])
                )
            ));

            if(!empty($usersName))
            {
                $response = $this->response(array(
                    'code' => 400,
                    'message' => 'Ese usuario ya esta registrado',
                    'data' => ''
                ));
                return $response;
            }

            if(!empty($usersEmail))
            {
                $response = $this->response(array(
                    'code' => 400,
                    'message' => 'Ese email ya esta registrado',
                    'data' => ''
                ));
                return $response;
            }

            $checkUserName = self::validatedName($input['name']);

            if($checkUserName['is'] == true)
            {
                $checkEmail = self::validatedEmail($input['email']);

                if($checkEmail == true)
                {
                    $checkPass = self::validatedPass($input['pass']);

                    if($checkPass['is'] == true)
                    {
                        $pass = self::SecurePass($input['pass']);

                        $user = new Model_Users();
                        $user->name = $input['name'];
                        $user->email = $input['email'];
                        $user->pass = $pass;
                        $user->victories = $input['victories'];
                        $user->defeats = $input['defeats'];
                        $user->save();

                        $response = $this->response(array(
                        'code' => 200,
                        'message' => 'Usuario creado con exito',
                        'data' => ''
                        ));
                        return $response;
                    }
                    else
                    {
                        $response = $this->response(array(
                            'code' => 400,
                            'message' => $checkPass['msgError'],
                            'data' => ''
                        ));
                        return $response;
                    }
                }
                else
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Formato de email no valido',
                        'data' => ''
                    ));
                    return $response;
                }
            }
            else
            {
                $response = $this->response(array(
                    'code' => 400,
                    'message' => $checkUserName['msgError'],
                    'data' => ''
                ));
                return $response;
            }    
    	}
        catch (Exception $e)
    	{
    		return self::ServerError();
    	}
    }

    public function get_ranking()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                $query = DB::select()->from('users')->order_by('victories','desc')->order_by('defeats', 'asc');

                $usersList = $query->execute();

                foreach ($usersList as $key => $user)
                {
                    $users[] = array(
                    'name' => $user['name'],
                    'victories' => $user['victories'],
                    'defeats' => $user['defeats']
                    );
                }

                $response = $this->response(array(
                    'code' => 200,
                    'message' => 'Usuarios obtenidos y ordenados con exito',
                    'data' => $users
                ));
                return $response; 
            }
            else
            {
                return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }

    public function get_login()
    {
        try
        {
            if(!isset($_GET['name']) || 
                !isset($_GET['pass'])) 
            {
                return self::EmptyError();
            }

            $input = $_GET;

            $checkPass = self::validatedPass($input['pass']);

            if($checkPass['is'] == true)
            {

                $users = Model_Users::find('all', array(
                    'where' => array(
                        array('name', $input['name'])
                    ),
                ));

                if(empty($users))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Usuario incorrecto',
                        'data' => ''
                    ));
                    return $response;
                }

                $userData = self::obtainData($users);

                if (password_verify($input['pass'], $userData['pass'])) 
                {
                    $token = self::encodeInfo($userData);

                    $displayInfo = array(
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'urlPhoto' => $userData['urlPhoto'],
                        'victories' => $userData['victories'],
                        'defeats' => $userData['defeats'],
                        'token' => $token,
                        'id' => $userData['id']
                    );

                    $listName = Model_Lists::find('all', array(
                        'where' => array(
                            array('name', 'My pieces'),
                            array('editable', 0),
                            array('id_user', $userData['id'])
                        ),
                    ));

                    if(empty($listName))
                    {
                        $list = new Model_Lists();
                        $list->name = 'My pieces';
                        $list->editable = 0;
                        $list->id_user = $userData['id'];
                        $list->save();
                    }

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'Usuario logeado',
                        'data' => json_encode($displayInfo)
                    ));
                    return $response;
                } 
                else 
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Clave incorrecta',
                        'data' => ''
                    ));
                    return $response;
                }
            }
            else
            {
                $response = $this->response(array(
                    'code' => 400,
                    'message' => $checkPass['msgError'],
                    'data' => ''
                ));
                return $response;
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }

    public function get_checkToRecoverPass()
    {
        try
        {
            if(!isset($_GET['name']) || 
                !isset($_GET['email'])) 
            {
                return self::EmptyError();
            }

            $input = $_GET;
            
            $users = Model_Users::find('all', array(
                'where' => array(
                    array('name', $input['name']),
                    array('email', $input['email'])
                ),
            ));

            if(empty($users))
            {
                $response = $this->response(array(
                    'code' => 400,
                    'message' => 'Usuario o email incorrectos',
                    'data' => ''
                ));
                return $response;
            }

            $userData = self::obtainData($users);

            $token = self::encodeInfo($userData);

            $response = $this->response(array(
                'code' => 200,
                'message' => 'Usuario encontrado',
                'data' => $token
            ));
            return $response;
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }
    
    public function post_editPass()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_POST['pass'])) 
                {
                    return self::EmptyError();
                }   

                $info = self::getUserInfo();

                $input = $_POST;

                $checkPass = self::validatedPass($input['pass']);

                if($checkPass['is'] == true)
                {
                    $pass = self::SecurePass($input['pass']);
                    
                    $query = DB::update('users');
                    $query->where('id', '=', $info['id']);
                    $query->value('pass', $pass);
                    $query->execute();

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'Contraseña cambiada con exito',
                        'data' => ''
                    ));
                }
                else
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => $checkPass['msgError'],
                        'data' => ''
                    ));
                    return $response;
                }
            }
            else
            {
                return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }

	public function post_delete()
	{
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_POST['id'])) 
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Debes rellenar todos los campos',
                        'data' => ''
                    ));
                    return $response;
                } 

                $info = self::getUserInfo();

                $input = $_POST;

                if($info['id'] == $input['id'])
                {
                    $user = Model_Users::find($info['id']);
                    $user->delete();

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'usuario borrado',
                        'data' => ''
                    ));
                    return $response;
                }
                else
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'No puedes borrar a otros usuarios',
                        'data' => ''
                    ));
                    return $response;
                } 
            }
            else
            {
                return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }    
	}

    public function post_editPhoto()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_POST['urlPhoto'])) 
                {
                    return self::EmptyError();
                }   

                $info = self::getUserInfo();

                $input = $_POST;

                $path = 'http://' . $_SERVER['SERVER_NAME'] . '/sapiens/public/assets/img/' . $input['urlPhoto'] . '.png';

                $query = DB::update('users');
                $query->where('id', '=', $info['id']);
                $query->value('urlPhoto', $path);
                $query->execute();

                $response = $this->response(array(
                    'code' => 200,
                    'message' => 'Foto cambiada con exito',
                    'data' => $path
                ));
                return $response;
            }
            else
            {
                return self::AuthError();
            }
        }
        catch (Exception $e)
        {
            return self::ServerError();
        }
    }
}
