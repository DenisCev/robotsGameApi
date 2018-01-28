<?php 
class Controller_List extends Controller_Base
{
    public function post_create()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
        		if(!isset($_POST['name']) || !isset($_POST['editable']))
                {
                    return self::EmptyError();
                }

                $info = self::getUserInfo();

                $input = $_POST;

                $checkName = self::validatedName($input['name']);

                if($checkName['is'] == true)
                {
                    $listName = Model_Lists::find('all', array(
                        'where' => array(
                            array('name', $input['name']),
                            array('id_user', $info['id'])
                        ),
                    ));

                    if(!empty($listName))
                    {
                        $response = $this->response(array(
                            'code' => 400,
                            'message' => 'Esa lista ya existe',
                            'data' => ''
                        ));
                        return $response;
                    }

                    $list = new Model_Lists();
                    $list->name = $input['name'];

                    if($input['editable'] == 1)
                    {
                        $list->editable = $input['editable'];
                    }
                    else
                    {
                        $response = $this->response(array(
                            'code' => 400,
                            'message' => 'No puedes crear ese tipo de listas',
                            'data' => ''
                        ));
                        return $response;
                    }

                    $list->users = Model_Users::find($info['id']);
                    $list->save();

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'lista creada',
                        'data' => ''
                    ));

                    return $response;
                }
                else
                {
                    $response = $this->response(array(
                    'code' => 400,
                    'message' => $checkName['msgError'],
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

    public function post_add()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_POST['id_piece']) || !isset($_POST['id_list']))
                {
                    self::EmptyError();
                }

                $info = self::getUserInfo();

                $input = $_POST;

                $list = Model_Lists::find($input['id_list']);

                if(empty($list))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa lista no existe',
                        'data' => ''
                    ));
                    return $response;
                }

                $piece = Model_Pieces::find($input['id_piece']);

                if(empty($piece))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa pieza no existe',
                        'data' => ''
                    ));
                    return $response;
                }

                $addName = Model_Add::find('all', array(
                    'where' => array(
                        array('id_list', $input['id_list']),
                        array('id_piece', $input['id_piece'])
                    ),
                ));

                if(!empty($addName))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa pieza ya existe en esta lista',
                        'data' => ''
                    ));
                    return $response;
                }

                $list = Model_Lists::find($input['id_list']);
                $list->pieces[] = Model_Pieces::find($input['id_piece']);
                $list->save();

                $response = $this->response(array(
                    'code' => 200,
                    'message' => 'Pieza agregada',
                    'data' => ''
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

    public function post_removeFromList()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_POST['id_piece']) || !isset($_POST['id_list']))
                {
                    return self::EmptyError();
                }

                $info = self::getUserInfo();

                $input = $_POST;

                $piecesFromList = Model_Add::find('all', array(
                    'where' => array(
                        array('id_list', $input['id_list']),
                        array('id_piece', $input['id_piece'])
                    ),
                ));

                if(!empty($piecesFromList)){
                    foreach ($piecesFromList as $key => $piece)
                    {
                        $piece->delete();
                    }

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'Pieza eliminada de la lista',
                        'data' => ''
                    ));
                    return $response;
                }
                else
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa pieza no existe en la lista',
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

    public function get_lists()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                $info = self::getUserInfo();

                $query = DB::select('*')->from('lists');
                $query->where('id_user', $info['id']);
                //$query->and_where('name', '!=', 'My pieces');
                $userLists = $query->execute();

                if(!empty($userLists))
                {
                    foreach ($userLists as $key => $list)
                    {
                        $lists[] = $list['name'];
                    }

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'Listas obtenidas',
                        'data' => $lists
                    ));
                    return $response;
                }
                else
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'No existen listas asociadas a esta cuenta',
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

    public function get_piecesFromList()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_GET['id_list']))
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Debes rellenar todos los campos',
                        'data' => ''
                    ));
                    return $response;
                }

                $info = self::getUserInfo();

                $input = $_GET;

                $piecesFromList = Model_Add::find('all', array(
                    'where' => array(
                        array('id_list', $input['id_list'])
                    ),
                ));

                if(!empty($piecesFromList)){
                    foreach ($piecesFromList as $key => $pieceList)
                    {
                        $piecesOfList[] = Model_Pieces::find($pieceList->id_piece);
                    }

                    foreach ($piecesOfList as $key => $piece)
                    {
                        $pieces[] = array(
                            "name" => $piece->name,
                            "side" => $piece->side,
                            "element" => $piece->element,
                            "rarity" => $piece->rarity,
                            "life" => $piece->life,
                            "damage" => $piece->damage,
                            "speed" => $piece->speed,
                            "cadence" => $piece->cadence,
                            "description" => $piece->description
                        );

                    }  

                    $response = $this->response(array(
                        'code' => 200,
                        'message' => 'Piezas encontradas',
                        'data' => $pieces
                    ));
                    return $response;
                }
                else
                {
                   $response = $this->response(array(
                        'code' => 400,
                        'message' => 'No existen piezas en esa la lista',
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

    public function post_edit()
    {
        try
        {
            $authenticated = self::requestAuthenticate();

            if($authenticated == true)
            {
                if(!isset($_POST['name']) || !isset($_POST['newName']))
                {
                    self::EmptyError();
                }

                $info = self::getUserInfo();
                $input = $_POST;

                $checkName = self::validatedName($input['newName']);

                if($checkName['is'] == true)
                {
                    $userLists = Model_Lists::find('all', array(
                        'where' => array(
                            array('id_user', $info['id']),
                            array('name', $input['name']),
                        ),
                    ));

                    if(!empty($userLists))
                    {
                        foreach ($userLists as $key => $list)
                        {
                            if($list->editable == 0){
                                $response = $this->response(array(
                                    'code' => 400,
                                    'message' => 'Esta lista no se puede editar',
                                    'data' => ''
                                ));
                                return $response;
                            }
                        }

                        $nameLists = Model_Lists::find('all', array(
                            'where' => array(
                                array('id_user', $info['id']),
                                array('name', $input['newName']),
                            ),
                        ));

                        if(!empty($nameLists))
                        {
                            $response = $this->response(array(
                                'code' => 400,
                                'message' => 'El nombre de esa lista ya existe',
                                'data' => ''
                            ));
                            return $response;
                        }

                        $query = DB::update('lists');
                        $query->where('name', '=', $input['name']);
                        $query->value('name', $input['newName']);
                        $query->execute();

                        $response = $this->response(array(
                            'code' => 200,
                            'message' => 'Nombre cambiado',
                            'data' => ''
                        ));
                    }
                    else
                    {
                        $response = $this->response(array(
                        'code' => 400,
                        'message' => 'Esa lista no existe',
                        'data' => ''
                        ));
                        return $response;
                    }
                }
                else
                {
                    $response = $this->response(array(
                        'code' => 400,
                        'message' => $checkName['msgError'],
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
                if(!isset($_POST['name']))
                {
                    return self::EmptyError();
                }

                $info = self::getUserInfo();
                $input = $_POST;
                
                $userLists = Model_Lists::find('all', array(
                    'where' => array(
                        array('id_user', $info['id']),
                        array('name', $input['name']),
                    ),
                ));

                if(!empty($userLists))
                {
                    foreach ($userLists as $key => $list)
                    {
                        if($list->editable == 0){
                            $response = $this->response(array(
                                'code' => 400,
                                'message' => 'Esta lista no se puede editar',
                                'data' => ''
                            ));
                            return $response;
                        }

                        if($list->editable == 1){
                            $list->delete();
                            $response = $this->response(array(
                                'code' => 200,
                                'message' => 'Lista borrada',
                                'data' => ''
                            ));
                            return $response;
                        }
                    } 
                }
                else
                {
                    $response = $this->response(array(
                    'code' => 400,
                    'message' => 'Esa lista no existe',
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
}