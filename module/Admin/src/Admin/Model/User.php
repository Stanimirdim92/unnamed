<?php
namespace Admin\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceManager;

class User implements InputFilterAwareInterface
{
    private $_inputFilter;

    /**
     * ServiceManager is a dependency injection we use for any additional methods requiring DB access.
     * Please, note that this is not the best way, but it does the job.
     *
     * @var $_serviceManager ServiceManager
     */
    private $_serviceManager;

    /**
     * @param Int $_id
     * @return int
     */
    private $_id;

    /**
     * @param String $_name
     * @return string
     */
    private $_name;

    /**
     * @param String $_surname
     * @return string
     */
    private $_surname;

    /**
     * @param Binary $_password
     * @return string
     */
    private $_password;

    /**
     * @param String $_email
     * @return string
     */
    private $_email;

    /**
     * @param Date $_birthDate
     * @return string
     */
    private $_birthDate;

    /**
     * @param Datetime $_lastLogin
     * @return string
     */
    private $_lastLogin;

    /**
     * @param Int $_deleted
     * @return boolean
     */
    private $_deleted;

    /**
     * @param String $_salt
     * @return string
     */
    private $_salt;

    /**
     * @param String $_image
     * @return string
     */
    private $_image;

    /**
     * @param Date $_registered
     * @return string
     */
    private $_registered;

    /**
     * @param Int $_hideEmail
     * @return boolean
     */
    private $_hideEmail;

    /**
     * @param String $_ip
     * @return string
     */
    private $_ip;

    /**
     * @param Int $_admin
     * @return int
     */
    private $_admin;

    /**
     * @param Int $_language
     * @return int
     */
    private $_language;

    /**
     * @param Int $_currency
     * @return int
     */
    private $_currency;

    public function setServiceManager($sm)
    {
        $this->_serviceManager = $sm;
    }

    public function exchangeArray($data)
    {
        $this->_id = (isset($data['id'])) ? $data['id'] : null;
        $this->_name = (isset($data['name'])) ? $data['name'] : null;
        $this->_surname = (isset($data['surname'])) ? $data['surname'] : null;
        $this->_password = (isset($data['password'])) ? $data['password'] : null;
        $this->_email = (isset($data['email'])) ? $data['email'] : null;
        $this->_birthDate = (isset($data['birthDate'])) ? $data['birthDate'] : null;
        $this->_lastLogin = (isset($data['lastLogin'])) ? $data['lastLogin'] : null;
        $this->_deleted = (isset($data['deleted'])) ? $data['deleted'] : null;
        $this->_salt = (isset($data['salt'])) ? $data['salt'] : null;
        $this->_image = (isset($data['image'])) ? $data['image'] : null;
        $this->_registered = (isset($data['registered'])) ? $data['registered'] : null;
        $this->_hideEmail = (isset($data['hideEmail'])) ? $data['hideEmail'] : null;
        $this->_ip = (isset($data['ip'])) ? $data['ip'] : null;
        $this->_admin = (isset($data['admin'])) ? $data['admin'] : null;
        $this->_language = (isset($data['language'])) ? $data['language'] : null;
        // $this->_currency = (isset($data['currency'])) ? $data['currency'] : null;
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
            $this->_serviceManager = $sm;
        }
    }
    
    /**
     * Get id
     */
    public function getId()
    {
        return $this->_id;
    }
    
    /**
     * Set id
     * @param int
     */
    public function setId(int $id)
    {
        $this->_id = $id;
    }
    
    
    /**
     * Set name
     * @param String $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Get name
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }
     
    /**
     * Set surname
     * @param String $surname
     */
    public function setSurname($surname)
    {
        $this->_surname = $surname;
    }

    /**
     * Get surname
     * @return String
     */
    public function getSurname()
    {
        return $this->_surname;
    }

    /**
     * Set password
     * @param String $password
     */
    public function setPassword($password)
    {
        $this->_password = $password;
    }

    /**
     * Get password
     * @return String
     */
    public function getPassword()
    {
        return $this->_password;
    }
     
    /**
     * Set email
     * @param String $email
     */
    public function setEmail($email)
    {
        $this->_email = $email;
    }

    /**
     * Get email
     * @return String
     */
    public function getEmail()
    {
        return $this->_email;
    }
     
    /**
     * Set BirthDate
     * @param String $birthDate
     */
    public function setBirthDate($birthDate)
    {
        $this->_birthDate = $birthDate;
    }

    /**
     * Get birthDate
     * @return String
     */
    public function getBirthDate()
    {
        return $this->_birthDate;
    }
     
    /**
     * Set lastLogin
     * @param String $lastLogin
     */
    public function setLastLogin($lastLogin)
    {
        $this->_lastLogin = $lastLogin;
    }

    /**
     * Get lastLogin
     * @return String
     */
    public function getLastLogin()
    {
        return $this->_lastLogin;
    }

    /**
     * Set deleted
     * @param Boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->_deleted = $deleted;
    }

    /**
     * Get deleted
     * @return Boolean
     */
    public function getDeleted()
    {
        return $this->_deleted;
    }
     
    /**
     * Set salt
     * @param String $salt
     */
    public function setSalt($salt)
    {
        $this->_salt = $salt;
    }

    /**
     * Get salt
     * @return String
     */
    public function getSalt()
    {
        return $this->_salt;
    }

    /**
     * Set image
     * @param String $image
     */
    public function setImage($image)
    {
        $this->_image = $image;
    }

    /**
     * Get image
     * @return String
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * Set registered
     * @param String $registered
     */
    public function setRegistered($registered)
    {
        $this->_registered = $registered;
    }

    /**
     * Get registered
     * @return String
     */
    public function getRegistered()
    {
        return $this->_registered;
    }

    /**
     * Set hideEmail
     * @param Boolean $hideEmail
     */
    public function setHideEmail($hideEmail)
    {
        $this->_hideEmail = $hideEmail;
    }

    /**
     * Get hideEmail
     * @return Boolean
     */
    public function getHideEmail()
    {
        return $this->_hideEmail;
    }

    /**
     * Set ip
     * @param String $ip
     */
    public function setIp($ip)
    {
        $this->_ip = $ip;
    }

    /**
     * Get ip
     * @return String
     */
    public function getIp()
    {
        return $this->_ip;
    }

    /**
     * Set language
     * @param Int $language
     */
    public function setLanguage($language)
    {
        $this->_language = $language;
    }

    /**
     * Get language
     * @return Int
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Set admin
     * @param Boolean $admin
     */
    public function setAdmin($admin)
    {
        $this->_admin = $admin;
    }

    /**
     * Get admin
     * @return Boolean
     */
    public function getAdmin()
    {
        return $this->_admin;
    }

    /**
     * Set currency
     * @param Int $currency
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Get currency
     * @return Int
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Get the related object from the DB
     */
    public function getLanguageObject()
    {
        try {
            return $this->serviceManager->get('LanguageTable')->fetchList(false, "id={$this->language}");
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
            return $this->serviceManager->get('AdministratorTable')->fetchList(false, "id={$this->admin}");
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * magic getter
     */
    public function __get($property)
    {
        return (property_exists($this, '_'. $property) ? $this->{'_'. $property} : null);
    }

    /**
     * magic setter
     */
    public function __set($property, $value)
    {
        if (property_exists($this, '_'. $property)) {
            $this->{'_'. $property} = $value;
        }
    }

    /**
     * magic property exists (isset) checker
     */
    public function __isset($property)
    {
        return (property_exists($this, '_'. $property));
    }
    
    /**
     * magic serializer
     */
    public function __sleep()
    {
        $skip = array("_serviceManager");
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key,$skip)) {
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
    public function getProperties($skip=array("_serviceManager"))
    {
        $returnValue = array();
        $data = get_class_vars(get_class($this));
        foreach ($data as $key=>$value) {
            if (!in_array($key,$skip)) {
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
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $inputFilter->add(array(
                'name'     => 'id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                "name"=>"name",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"surname",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"password",
                "required" => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 3,
                            'max' => 20,
                        ),
                    ),
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"email",
                "required" => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                "validators" => array(
                    array(
                        'name' => 'EmailAddress',
                        'options' => array(
                            'messages' => array('emailAddressInvalidFormat' => "Email address doesn't appear to be valid."),
                        ),
                    ),
                    array('name' => 'NotEmpty'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"birthDate",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"lastLogin",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"deleted",
                "required" => false,
            ));
            $inputFilter->add(array(
                "name"=>"salt",
                "required" => false,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"image",
                "required" => false,
            ));
            $inputFilter->add(array(
                "name"=>"registered",
                "required" => false,
            ));
            $inputFilter->add(array(
                "name"=>"hideEmail",
                "required" => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"ip",
                "required" => false,
            ));
            $inputFilter->add(array(
                "name"=>"admin",
                "required" => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"language",
                "required" => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                "name"=>"currency",
                "required" => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $this->_inputFilter = $inputFilter;
        }
        return $this->_inputFilter;
    }
    
    /**
     * This method is a copy constructor that will return a copy object (except for the id field)
     * Note that this method will not save the object
     */
    public function getCopy()
    {
        $clone = new self();
        $clone->setName($this->_name);
        $clone->setSurname($this->_surname);
        $clone->setPassword($this->_password);
        $clone->setEmail($this->_email);
        $clone->setBirthDate($this->_birthDate);
        $clone->setLastLogin($this->_lastLogin);
        $clone->setDeleted($this->_deleted);
        $clone->setSalt($this->_salt);
        $clone->setImage($this->_image);
        $clone->setRegistered($this->_registered);
        $clone->setHideEmail($this->_hideEmail);
        $clone->setIp($this->_ip);
        $clone->setAdmin($this->_admin);
        $clone->setLanguage($this->_language);
        $clone->setCurrency($this->_currency);
        return $clone;
    }

    /**
     * toString method
     */
    public function toString()
    {
        return $this->_name." ".$this->_surname;
    }

    public function export($path="/userfiles/userExports")
    {
        require_once($_SERVER["DOCUMENT_ROOT"]."/zend/vendor/CodePlex/PHPExcel.php");
        $filename = md5($this->id." ".rand(10000,2000000)).".xlsx";
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

        $colLetters = array('A', 'B', 'C', 'D','E', 'F');
        foreach ($colLetters as $colLetter) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($colLetter)->setWidth(25);
        }
        $cellTitles = array('ID', "Name", "Surname", "Email", "Last login", "Registered on");
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
