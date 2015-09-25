<?php
/**
 * MIT License.
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.13
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Content;
use Zend\View\Model\JsonModel;
use Admin\Form\ContentForm;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;

final class ContentController extends IndexController
{
    /**
     * @var array
     */
    protected $acceptCriteria = [
        'Zend\View\Model\JsonModel' => ['application/json'],
        'Zend\View\Model\ViewModel' => ['text/html'],
    ];

    /**
     * @var ContentForm $contentForm
     */
    private $contentForm = null;

    /**
     * @param ContentForm $contentForm
     */
    public function __construct(ContentForm $contentForm = null)
    {
        parent::__construct();

        $this->contentForm = $contentForm;
    }

    /**
     * Initialize any variables before controller actions
     *
     * @param MvcEvent $e
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        parent::onDispatch($e);
        $this->addBreadcrumb(["reference"=>"/admin/content", "name"=>$this->translate("CONTENTS")]);
    }

    /**
     * This action shows the list of all contents
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        $this->getView()->setTemplate("admin/content/index");
        if ((int) $this->getParam("id", 0) === 1) {
            $this->getView()->contents = $this->getTable("content")->fetchList(false, [], "type='1' AND content.language='".$this->language()."'", null, null,  "content.date DESC");
        } else {
            $this->getView()->contents = $this->getTable("content")->fetchJoin(false, "menu", [], [], "content.menu=menu.id", "inner", "type='0' AND content.language='".$this->language()."'", null, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }
        return $this->getView();
    }

    /**
     * This action serves for adding a new object of type Content
     *
     * @return ViewModel
     */
    protected function addAction()
    {
        $this->getView()->setTemplate("admin/content/add");
        $this->initForm($this->translate("ADD_NEW_CONTENT"), null);
        $this->addBreadcrumb(["reference"=>"/admin/content/add", "name"=>$this->translate("ADD_NEW_CONTENT")]);
        return $this->getView();
    }

    /**
     * This action presents a modify form for Content object with a given id and session language
     * Upon POST the form is processed and saved
     *
     * @return ViewModel
     */
    protected function modifyAction()
    {
        $this->acceptableviewmodelselector($this->acceptCriteria);

        $this->getView()->setTemplate("admin/content/modify");
        $content = $this->getTable("content")->getContent((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/modify/{$content->getId()}", "name"=> $this->translate("MODIFY_CONTENT")." &laquo;".$content->getTitle()."&raquo;"]);
        $this->initForm($this->translate("MODIFY_CONTENT"), $content);
        return $this->getView();
    }

    /**
     * this action deletes a content
     */
    protected function deleteAction()
    {
        $this->getTable("content")->deleteContent((int)$this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_CONTENT_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * this action shows content details
     *
     * @return ViewModel
     */
    protected function detailAction()
    {
        $this->getView()->setTemplate("admin/content/detail");
        $content = $this->getTable("content")->getContent((int)$this->getParam("id", 0), $this->language())->current();
        $this->getView()->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/detail/".$content->getId()."", "name"=>"&laquo;". $content->getTitle()."&raquo; ".$this->translate("DETAILS")]);
        return $this->getView();
    }

    protected function deactivateAction()
    {
        $this->getTable("content")->toggleActiveContent((int)$this->getParam("id", 0), 0);
        $this->setLayoutMessages($this->translate("CONTENT_DISABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    protected function activateAction()
    {
        $this->getTable("content")->toggleActiveContent((int)$this->getParam("id", 0), 1);
        $this->setLayoutMessages($this->translate("CONTENT_ENABLE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * This action will clone the object
     */
    protected function cloneAction()
    {
        $this->getTable("content")->duplicate((int)$this->getParam("id", 0), $this->language())->current();
        $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
    }

    /**
     * This is common function used by add and edit actions
     *
     * @param string $label button title
     * @param  Content $content
     */
    private function initForm($label = '', Content $content = null)
    {
        if (!$content instanceof Content) {
            $content = new Content([]);
        }

        /**
         * @var $form ContentForm
         */
        $form = $this->contentForm;
        $form->bind($content);
        $form->get("submit")->setValue($label);
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
                    $name = $userData->getIdentity()->name." ".$userData->getIdentity()->surname;
                } else {
                    $name = "Admin";
                }

                /**
                 * We only need the name. All images ar stored in the same folder, based on the month and year
                 */
                $formData->setPreview($formData->getPreview()["name"]);
                $formData->setAuthor($name);
                $this->getTable("content")->saveContent($content);
                $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
                return $this->redirect()->toRoute('admin/default', ['controller' => 'content']);
            } else {
                return $this->setLayoutMessages($form->getMessages(), "error");
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
     * Deleted image with from a given src
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
                // @codeCoverageIgnoreStart
                if (is_file("public".$data["img"])) {
                    unlink("public".$data["img"]);
                    $status = true;
                }
                // @codeCoverageIgnoreEnd
            }
        }
        return $status;
    }

    /**
     * Get all files from all folders and list them in the gallery
     * getcwd() is there to make the work with images path easier
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
     * Upload all images async
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
     * @return array
     */
    private function uploadFiles(Http $adapter)
    {
        $uploadStatus = [];

        foreach ($adapter->getFileInfo() as $key => $file) {
            if ($key != "preview") {
                if (!$adapter->isValid($file["name"])) {
                    foreach ($adapter->getMessages() as $key => $msg) {
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
