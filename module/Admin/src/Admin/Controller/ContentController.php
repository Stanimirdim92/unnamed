<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Entity\Content;
use Zend\View\Model\JsonModel;
use Admin\Form\ContentForm;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Mvc\MvcEvent;

final class ContentController extends BaseController
{
    /**
     * @var array
     */
    protected $acceptCriteria = [
        'Zend\View\Model\JsonModel' => ['application/json'],
        'Zend\View\Model\ViewModel' => ['text/html'],
    ];

    /**
     * @var ContentForm
     */
    private $contentForm;

    /**
     * @var \Admin\Model\ContentTable
     */
    private $contentTable;


    /**
     * @param ContentForm $contentForm
     */
    public function __construct(ContentForm $contentForm)
    {
        parent::__construct();

        $this->contentForm = $contentForm;
    }

    /**
     * @param MvcEvent $event
     *
     * @return mixed|void
     */
    public function onDispatch(MvcEvent $event)
    {
        $this->addBreadcrumb(["reference"=>"/admin/content", "name"=>$this->translate("CONTENTS")]);
        $this->contentTable = $this->getTable("Admin\\Model\\ContentTable");

        parent::onDispatch($event);
    }

    /**
     * This action shows the list of all contents.
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/content/index");

        $table = $this->contentTable;

        if ((int) $this->getParam("id", 0) === 1) {
            $query = $table->queryBuilder()->select(["c"])
                   ->from('Admin\Entity\Content', 'c')
                   ->where("c.type = 1 AND c.language = :language")
                   ->setParameter(":language", (int) $this->language())
                   ->orderBy("c.date DESC");
            $paginator = $table->preparePagination($query, false);
        } else {
            $query = $table->queryBuilder()->getEntityManager()->createQuery("SELECT c FROM Admin\Entity\Content AS c LEFT JOIN Admin\Entity\Menu AS m WITH c.menu=m.id WHERE c.type = 0 AND c.language = {$this->language()} ORDER BY m.parent ASC, m.menuOrder ASC, c.date DESC");
            $paginator = $table->preparePagination($query, true);
        }

        $paginator->setCurrentPageNumber((int)$this->getParam("page", 1));
        $paginator->setItemCountPerPage($this->systemSettings('posts', 'language'));
        $this->getView()->paginator = $paginator;

        return $this->getView();
    }

    /**
     * This action serves for adding a new object of type Content.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/content/add");
        $this->initForm();
        $this->addBreadcrumb(["reference"=>"/admin/content/add", "name"=>$this->translate("ADD_NEW_CONTENT")]);

        return $this->getView();
    }

    /**
     * This action presents a edit form for Content object with a given id and session language.
     * Upon POST the form is processed and saved.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function editAction()
    {
        $this->acceptableviewmodelselector($this->acceptCriteria);

        $this->getView()->setTemplate("admin/content/edit");
        $content = $this->contentTable->getContent((int)$this->getParam("id", 0), $this->language());
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/edit/{$content->getId()}", "name"=> $this->translate("EDIT_CONTENT")." &laquo;".$content->getTitle()."&raquo;"]);
        $this->initForm($content);

        return $this->getView();
    }

    /**
     * this action deletes a content.
     */
    protected function deleteAction()
    {
        $this->contentTable->deleteContent((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_CONTENT_SUCCESS"), "success");
    }

    /**
     * this action shows content details.
     *
     * @return \Zend\View\Model\ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/content/detail");
        $content = $this->contentTable->getContent((int)$this->getParam("id", 0), $this->language());
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/detail/".$content->getId()."", "name"=>"&laquo;". $content->getTitle()."&raquo; ".$this->translate("DETAILS")]);

        return $this->getView();
    }

    protected function deactivateAction()
    {
        $this->contentTable->toggleActiveContent((int)$this->getParam("id", 0), $this->language(), 0);
        $this->setLayoutMessages($this->translate("CONTENT_DISABLE_SUCCESS"), "success");
    }

    protected function activateAction()
    {
        $this->contentTable->toggleActiveContent((int)$this->getParam("id", 0), $this->language(), 1);
        $this->setLayoutMessages($this->translate("CONTENT_ENABLE_SUCCESS"), "success");
    }

    /**
     * This is common function used by add and edit actions.
     *
     * @param Content $content
     */
    private function initForm(Content $content = null)
    {
        if (!$content instanceof Content) {
            $content = new Content([]);
        }

        /**
         * @var $form ContentForm
         */
        $form = $this->contentForm;
        $form->bind($content);
        $this->getView()->form = $form;

        return $this->form($form, $content);
    }

    /**
     * @param ContentForm $form
     * @param Content $content
     */
    private function form(ContentForm $form, Content $content)
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $formData = $form->getData();
                $userData = $this->UserData();

                if ($userData->checkIdentity(false, $this->translate("ERROR_AUTHORIZATION"))) {
                    $content->setAuthor($userData->getIdentity()->name." ".$userData->getIdentity()->surname);
                } else {
                    $content->setAuthor("Admin");
                }

                /*
                 * We only need the name. All images ar stored in the same folder, based on the month and year
                 */
                $content->setPreview($formData->getPreview()["name"]);
                $this->contentTable->saveContent($content);
                $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
            } else {
                $this->setLayoutMessages($form->getMessages(), "error");
            }
        }
    }

    /**
     * @return JsonModel
     */
    protected function uploadAction()
    {
        $request = $this->getRequest();
        $data = [];

        if ($request->isXmlHttpRequest()) {
            $data = $this->prepareImages();
        }

        return new JsonModel($data);
    }

    /**
     * Deleted image with from a given src.
     *
     * @method deleteimageAction
     *
     * @return bool
     */
    protected function deleteimageAction()
    {
        $request = $this->getRequest();
        $status = false;

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            if ($request->isXmlHttpRequest()) {
                if (is_file("public".$data["img"])) {
                    unlink("public".$data["img"]);
                    $status = true;
                }
            }
        }
        return $status;
    }

    /**
     * Get all files from all folders and list them in the gallery
     * getcwd() is there to make the work with images path easier.
     *
     * @return JsonModel
     */
    protected function filesAction()
    {
        chdir(getcwd()."/public/");
        if (!is_dir('userfiles/'.date("Y_M").'/images/')) {
            mkdir('userfiles/'.date("Y_M").'/images/', 0750, true);
        }
        $this->getView()->setTerminal(true);
        $dir = new \RecursiveDirectoryIterator('userfiles/', \FilesystemIterator::SKIP_DOTS);
        $it  = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $it->setMaxDepth(50);
        $files = [];
        $i = 0;
        foreach ($it as $file) {
            if ($file->isFile()) {
                $files[$i]["filelink"] = DIRECTORY_SEPARATOR.$file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
                $files[$i]["filename"] = $file->getFilename();
                $i++;
            }
        }
        chdir(dirname(getcwd()));
        $model = new JsonModel();
        $model->setVariables(["files" => $files]);
        return $model;
    }

    /**
     * Upload all images async.
     *
     * @return array
     */
    private function prepareImages()
    {
        $adapter = new Http();
        $size = new Size(['min'=>'10kB', 'max'=>'5MB', 'useByteString' => true]);
        $extension = new Extension(['jpg', 'gif', 'png', 'jpeg', 'bmp', 'webp', 'svg'], true);

        $adapter->setValidators([$size, new IsImage(), $extension]);

        if (!is_dir('public/userfiles/'.date("Y_M").'/images/')) {
            mkdir('public/userfiles/'.date("Y_M").'/images/', 0750, true);
        }

        $adapter->setDestination('public/userfiles/'.date("Y_M").'/images/');
        return $this->uploadFiles($adapter);
    }

    /**
     * @param  Http $adapter
     *
     * @return array
     */
    private function uploadFiles(Http $adapter)
    {
        $uploadStatus = [];

        foreach ($adapter->getFileInfo() as $key => $file) {
            if ($key != "preview") {
                if (!$adapter->isValid($file["name"])) {
                    foreach ($adapter->getMessages() as $msg) {
                        $uploadStatus["errorFiles"][] = $file["name"]." ".strtolower($msg);
                    }
                }

                // @codeCoverageIgnoreStart
                $adapter->receive($file["name"]);
                if (!$adapter->isReceived($file["name"]) && $adapter->isUploaded($file["name"])) {
                    $uploadStatus["errorFiles"][] = $file["name"]." was not uploaded";
                } else {
                    $uploadStatus["successFiles"][] = $file["name"]." was successfully uploaded";
                }
                // @codeCoverageIgnoreEnd
            }
        }
        return $uploadStatus;
    }
}
