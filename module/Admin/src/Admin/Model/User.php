<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class User implements InputFilterAwareInterface
{
    private $inputFilter;

    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access.
     * Please, note that this is not the best way, but it does the job.
     *
     * @var $serviceManager ServiceManager
     */
    private $serviceManager;

    /**
     * @param Int $id
     * @return int
     */
    private $id;

    /**
     * @param String $name
     * @return string
     */
    private $name;

    /**
     * @param String $surname
     * @return string
     */
    private $surname;

    /**
     * @param Binary $password
     * @return string
     */
    private $password;

    /**
     * @param String $email
     * @return string
     */
    private $email;

    /**
     * @param Date $birthDate
     * @return string
     */
    private $birthDate;

    /**
     * @param Datetime $lastLogin
     * @return string
     */
    private $lastLogin;

    /**
     * @param Int $deleted
     * @return boolean
     */
    private $deleted;

    /**
     * @param String $salt
     * @return string
     */
    private $salt;

    /**
     * @param String $image
     * @return string
     */
    private $image;

    /**
     * @param Date $registered
     * @return string
     */
    private $registered;

    /**
     * @param Int $hideEmail
     * @return boolean
     */
    private $hideEmail;

    /**
     * @param String $ip
     * @return string
     */
    private $ip;

    /**
     * @param Int $admin
     * @return int
     */
    private $admin;

    /**
     * @param Int $language
     * @return int
     */
    private $language;

    /**
     * @param Int $currency
     * @return int
     */
    private $currency;

    public function setServiceManager($sm)
    {
        $this->serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->surname = (isset($data['surname'])) ? $data['surname'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->birthDate = (isset($data['birthDate'])) ? $data['birthDate'] : null;
        $this->lastLogin = (isset($data['lastLogin'])) ? $data['lastLogin'] : null;
        $this->deleted = (isset($data['deleted'])) ? $data['deleted'] : null;
        $this->salt = (isset($data['salt'])) ? $data['salt'] : null;
        $this->image = (isset($data['image'])) ? $data['image'] : null;
        $this->registered = (isset($data['registered'])) ? $data['registered'] : null;
        $this->hideEmail = (isset($data['hideEmail'])) ? $data['hideEmail'] : null;
        $this->ip = (isset($data['ip'])) ? $data['ip'] : null;
        $this->admin = (isset($data['admin'])) ? $data['admin'] : null;
        $this->language = (isset($data['language'])) ? $data['language'] : null;
        // $this->currency = (isset($data['currency'])) ? $data['currency'] : null;
    }

    /**
     * constructor
     */
    public function __construct(array $options = null, ServiceManager $sm = null)
    {
        if (is_array($options) && $options instanceof Traversable) {
            $this->exchangeArray($options);
        }
        if ($sm != null) {
            $this->serviceManager = $sm;
        }
    }

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @param int
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }


    /**
     * Set name
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     * @param String $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname
     * @return String
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set password
     * @param String $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     * @param String $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     * @return String
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set BirthDate
     * @param String $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    }

    /**
     * Get birthDate
     * @return String
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set lastLogin
     * @param String $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;
    }

    /**
     * Get lastLogin
     * @return String
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set deleted
     * @param Boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     * @return Boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set salt
     * @param String $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     * @return String
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set image
     * @param String $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get image
     * @return String
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set registered
     * @param String $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
    }

    /**
     * Get registered
     * @return String
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set hideEmail
     * @param Boolean $hideEmail
     */
    public function setHideEmail($hideEmail)
    {
        $this->hideEmail = $hideEmail;
    }

    /**
     * Get hideEmail
     * @return Boolean
     */
    public function getHideEmail()
    {
        return $this->hideEmail;
    }

    /**
     * Set ip
     * @param String $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     * @return String
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set language
     * @param Int $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     * @return Int
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set admin
     * @param Boolean $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * Get admin
     * @return Boolean
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set currency
     * @param Int $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     * @return Int
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the related object from the DB
     */
    public function getLanguageObject()
    {
        try {
            return $this->serviceManager->get('LanguageTable')->getLanguage($this->language);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the related object from the DB
     */
    public function getAdministratorObject()
    {
        try {
            return $this->serviceManager->get('AdministratorTable')->getAdministrator($this->admin);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * magic getter
     */
    public function __get($property)
    {
        return (property_exists($this, $property) ? $this->{$property} : null);
    }

    /**
     * magic setter
     */
    public function __set($property, $value)
    {
        if (property_exists($this, $property)) {
            $this->{$property} = $value;
        }
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, $property));
    }

    /**
     * magic serializer
     */
    public function __sleep()
    {
        $skip = ["serviceManager"];
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key, $skip)) {
                $returnValue[] = $key;
            }
        }
        return $returnValue;
    }

    /**
     * magic unserializer (ideally we should recreate the connection to service manager)
     */
    public function __wakeup()
    {
    }

    /**
     * this is a handy function for encoding the object to json for transfer purposes
     */
    public function getProperties($skip=["serviceManager"])
    {
        $returnValue = [];
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key, $skip)) {
                $returnValue[$key]=$this->$key;
            }
        }
        return $returnValue;
    }
    /**
     * encode this object as json, we do not include the mapper properties
     */
    public function toJson()
    {
        return \Zend\Json\Json::encode($this->getProperties());
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add([
                'name'     => 'id',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]);

            $inputFilter->add([
                "name"=>"name",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"surname",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"password",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 20,
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"email",
                "required" => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                "validators" => [
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'messages' => ['emailAddressInvalidFormat' => "Email address doesn't appear to be valid."],
                        ],
                    ],
                    ['name' => 'NotEmpty'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"birthDate",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"lastLogin",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"deleted",
                "required" => false,
            ]);
            $inputFilter->add([
                "name"=>"salt",
                "required" => false,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"image",
                "required" => false,
            ]);
            $inputFilter->add([
                "name"=>"registered",
                "required" => false,
            ]);
            $inputFilter->add([
                "name"=>"hideEmail",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"ip",
                "required" => false,
            ]);
            $inputFilter->add([
                "name"=>"admin",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"language",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);
            $inputFilter->add([
                "name"=>"currency",
                "required" => false,
                'filters' => [
                    ['name' => 'Int'],
                ],
            ]);
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setName($this->name);
        $clone->setSurname($this->surname);
        $clone->setPassword($this->password);
        $clone->setEmail($this->email);
        $clone->setBirthDate($this->birthDate);
        $clone->setLastLogin($this->lastLogin);
        $clone->setDeleted($this->deleted);
        $clone->setSalt($this->salt);
        $clone->setImage($this->image);
        $clone->setRegistered($this->registered);
        $clone->setHideEmail($this->hideEmail);
        $clone->setIp($this->ip);
        $clone->setAdmin($this->admin);
        $clone->setLanguage($this->language);
        $clone->setCurrency($this->currency);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->name." ".$this->surname;
    }

    public function export($path="/userfiles/userExports")
    {
        require_once($_SERVER["DOCUMENT_ROOT"]."/zend/vendor/CodePlex/PHPExcel.php");
        $filename = md5($this->id." ".rand(10000, 2000000)).".xlsx";
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("SEO optimizer Excel Export Plugin")
        ->setTitle("Office 2007 XLS Export Document")
        ->setSubject("Office 2007 XLS Export Document")
        ->setDescription("Excel Autoexport");
        $tab = 0;
        $objPHPExcel->createSheet();
        $sheet = $objPHPExcel->setActiveSheetIndex($tab ++);
        $sheet = $objPHPExcel->getActiveSheet();
        $sheet->setTitle("User's auto export info");

        $colLetters = ['A', 'B', 'C', 'D','E', 'F'];
        foreach ($colLetters as $colLetter) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($colLetter)->setWidth(25);
        }
        $cellTitles = ['ID', "Name", "Surname", "Email", "Last login", "Registered on"];
        $cellCol = 0;
        foreach ($cellTitles as $cellTitle) {
            $sheet->setCellValueExplicitByColumnAndRow($cellCol++, 1, $cellTitle);
        }

        $col = 0;
        $row = 2;
        $users = $this->serviceManager->get("UserTable")->fetchList(false, "deleted = '0'", "id DESC");
        foreach ($users as $user) {
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $user->getId, \PHPExcel_Cell_DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $user->getName());
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $user->getSurname());
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $user->getEmail());
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $user->getLastLogin());
            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $user->getRegistered());
            $col = 0; // reset column for next user
            $row++;
        }
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($path."/".$filename);
        return $filename;
    }
}
