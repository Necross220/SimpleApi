<?php

class Contacts extends connection
{

    public function process(array $params): void
    {

        //REUSABLE VARIABLES
        $method = $params[1] ?? 'read';
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        //Sanitizing inputs
        $name = isset($_POST['name']) ? filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
        $lastname = isset($_POST['lastname']) ? filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : '';

        //Sub-Sanitized inputs -> Numbers
        $phones = $_POST['phones'] ? : [];

        switch ($method):
            case 'create':
                $this->create($name, $lastname, $email, $phones);
                break;
            case 'update':
                $this->update($id, $name, $lastname, $email);
                break;
            case 'delete':
                //READ COMMENTS
                $this->delete($id);
                break;
            default: //read
                $this->read($id);
                break;
        endswitch;

    }

    /**
     * Creates contacts'
     * @param string $name The URL address to be parsed
     * @param string $lastname The URL address to be parsed
     * @param string $email The URL address to be parsed
     * @return void echoes JSON results
     * @throws JsonException
     */
    private function create(string $name, string $lastname, string $email, array $phones): void
    {

        //Checks if the name and lastname are in the correct format
        $this->isValidFullname($name, $lastname);

        //Checks the email provided matches an email patter
        $this->isValidEmail($email);

        try {

            $data_set = $this->conn->prepare('CALL CreateContacts(:name, :lastname, :email)');
            $data_set->bindParam(':name', $name, PDO::PARAM_STR);
            $data_set->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $data_set->bindParam(':email', $email, PDO::PARAM_STR);
            $data_set->execute();
            $contact_id = $this->collection($data_set)[0]['last_id'];
            $data_set->closeCursor();

            foreach($phones as $phone){
                $data_set = $this->conn->prepare('CALL CreatePhones(:contact_id, :number)');
                $data_set->bindParam(':contact_id', $contact_id, PDO::PARAM_INT);
                $data_set->bindParam(':number', $phone);
                $data_set->execute();
            }

            echo json_encode([
                'type' => 'success',
                'title' => 'Exito',
                'message' => 'Se ha creado el contacto'
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);

        } catch (Exception $exception) {
            echo json_encode([
                'type' => 'error',
                'title' => 'Algo malo sucedio',
                'message' => $exception->getMessage()
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
        }
    }

    /**
     * Reads contacts' information
     * @param int $id
     * @return void Echoes JSON of contacts
     * @throws JsonException
     */
    private function read(int $id): void
    {
        try {

            $this->isValidContact($id);
            $data_set = $this->conn->prepare('CALL ReadContacts (:Id)');
            $data_set->bindParam(':Id', $id, PDO::PARAM_INT);
            $data_set->execute();

            $contacts = $this->collection($data_set);

            $data_set->closeCursor();

            foreach ($contacts as $key => $contact){
                $data_set = $this->conn->prepare('CALL ReadPhones(:Id)');
                $data_set->bindParam(':Id', $contact['id'], PDO::PARAM_INT);
                $data_set->execute();
                $number = $this->collection($data_set);
                $contacts[$key]['numbers'] = $number;
                $data_set->closeCursor();
            }

            echo json_encode($contacts, JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);

        } catch (Exception $exception) {
            echo json_encode([
                'type' => 'error',
                'title' => 'Algo malo sucedio',
                'message' => $exception->getMessage()
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
        }
    }

    /**
     * Updates contacts' information
     * @param int $id The URL address to be parsed
     * @param string $name The URL address to be parsed
     * @param string $lastname The URL address to be parsed
     * @param string $email The URL address to be parsed
     * @return void Echoes JSON results
     * @throws JsonException
     */
    private function update(int $id, string $name, string $lastname, string $email): void
    {

        //Checks if the name and lastname are in the correct format
        $this->isValidFullname($name, $lastname);

        //Checks the email provided matches an email patter
        $this->isValidEmail($email);

        //Checks the id is between boundaries
        $this->isValidContact($id);

        try {

            $data_set = $this->conn->prepare('CALL UpdateContacts(:id, :name, :lastname, :email)');
            $data_set->bindParam(':id', $id, PDO::PARAM_STR);
            $data_set->bindParam(':name', $name, PDO::PARAM_STR);
            $data_set->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $data_set->bindParam(':email', $email, PDO::PARAM_STR);
            $data_set->execute();

            echo json_encode([
                'type' => 'sucess',
                'title' => 'Exito',
                'message' => 'Se ha actualizado el contacto'
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);

        } catch (Exception $exception) {
            echo json_encode([
                'type' => 'error',
                'title' => 'Algo malo sucedio',
                'message' => $exception->getMessage()
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
        }
    }

    /**
     * Updates contacts' information
     * @param int $id The URL address to be parsed
     * @return void Echoes JSON results
     * @throws JsonException
     */
    private function delete($id)
    {

        //Checks the id is between boundaries
        $this->isValidContact($id);

        try {

            /*
                Best practices and it's guidelines suggests that we don't delete info,
                but change the record's status to 2 <- Inactive, the "reading" store procedure contemplates to read
                records' status = 1 <- Active.
            */
            $data_set = $this->conn->prepare('CALL DeleteContacts(:id)');
            $data_set->bindParam(':id', $id, PDO::PARAM_STR);
            $data_set->execute();

            echo json_encode([
                'type' => 'sucess',
                'title' => 'Exito',
                'message' => 'Se ha eliminado el contacto'
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);

        } catch (Exception $exception) {
            echo json_encode([
                'type' => 'error',
                'title' => 'Algo malo sucedio',
                'message' => $exception->getMessage()
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
        }
    }

    /**
     * Updates contacts' information
     * @param int $id Checks if the contact id is valid
     * @return void Echoes JSON results
     * @throws JsonException
     */
    public function isValidContact($id): void
    {
        if (is_nan($id) || $id < 0) {
            echo json_encode([
                'type' => 'error',
                'title' => 'Atencion',
                'message' => 'El Id del contacto debe ser un numero positivo'
            ], JSON_THROW_ON_ERROR);
            exit();
        }

    }

    /**
     * Updates contacts' information
     * @param int $name Checks if the contact name is valid
     * @param int $lastname Checks if the lastname id is valid
     * @return void Echoes JSON results
     * @throws JsonException
     */
    public function isValidFullname($name, $lastname): void
    {

        if ($name === "" || strlen($name) > 30) {
            echo json_encode([
                'type' => 'info',
                'title' => 'Atencion',
                'message' => 'El nombre del contacto no puede estar vacio ni exceder los 30 caracteres'
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
            exit();
        }

        if ($lastname === "" || strlen($lastname) > 30) {
            echo json_encode([
                'type' => 'info',
                'title' => 'Atencion',
                'message' => 'El nombre del contacto no puede estar vacio ni exceder los 30 caracteres'
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
            exit();
        }

    }

    /**
     * Updates contacts' information
     * @param int $email Checks if the contact email is valid
     * @return void Echoes JSON results
     * @throws JsonException
     */
    public function isValidEmail($email): void
    {
        $is_email = preg_match("/^([a-z0-9_\-]+)(\.[a-z0-9_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email);

        if ($is_email === 0) {
            echo json_encode([
                'type' => 'info',
                'title' => 'Atencion',
                'message' => 'El email del contacto no puede estar vacio ni exceder los 30 caracteres'
            ], JSON_THROW_ON_ERROR, JSON_FORCE_OBJECT);
            exit();
        }

    }

}