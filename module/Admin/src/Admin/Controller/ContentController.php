<?php
/**
 * MIT License
 * ===========
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
 * @version    0.0.5
 * @link       TBA
 */

namespace Admin\Controller;

use Admin\Model\Content;
use Zend\View\Model\JsonModel;
use Zend\Json\Json;
use Admin\Form\ContentForm;
use Zend\File\Transfer\Adapter\Http;

class ContentController extends IndexController
{
    /**
     * @var Admin\Form\ContentForm $contentForm
     */
    private $contentForm = null;

    /**
     * @param Admin\Form\ContentForm $contentForm
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
        $this->addBreadcrumb(["reference"=>"/admin/content", "name"=>$this->translate("CONTENTS")]);
        parent::onDispatch($e);
    }

    /**
     * This action shows the list of all contents
     */
    public function indexAction()
    {
        $this->view->setTemplate("admin/content/index");
        if ((int) $this->getParam("type", 0) === 1) {
            $this->view->contents = $this->getTable("content")->fetchList(false, [], "type='1' AND content.language='".$this->language()."'", null, null,  "content.date DESC");
        } else {
            $this->view->contents = $this->getTable("content")->fetchJoin(false, "menu", [], [], "content.menu=menu.id", "inner", "type='0' AND content.language='".$this->language()."'", null, "menu.parent ASC, menu.menuOrder ASC, content.date DESC");
        }
        return $this->view;
    }

    /**
     * This action serves for adding a new object of type Content
     */
    protected function addAction()
    {
        $this->view->setTemplate("admin/content/add");
        $this->initForm($this->translate("ADD_NEW_CONTENT"), null);
        $this->addBreadcrumb(["reference"=>"/admin/content/add", "name"=>$this->translate("ADD_NEW_CONTENT")]);
        return $this->view;
    }

    /**
     * This action presents a modify form for Content object with a given id and session language
     * Upon POST the form is processed and saved
     */
    protected function modifyAction()
    {
        $this->view->setTemplate("admin/content/modify");
        $content = $this->getTable("content")->getContent($this->getParam("id", 0), $this->language())->current();
        $this->view->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/modify/{$content->getId()}", "name"=> $this->translate("MODIFY_CONTENT")." &laquo;".$content->getTitle()."&raquo;"]);
        $this->initForm($this->translate("MODIFY"), $content);
        return $this->view;
    }

    /**
     * this action deletes a content object with a provided id and session language
     */
    protected function deleteAction()
    {
        $content = $this->getTable("content")->deleteContent($this->getParam("id", 0), $this->language());
        $this->setLayoutMessages($this->translate("DELETE_CONTENT_SUCCESS"), "success");
        return $this->redirect()->toRoute('admin', ['controller' => 'content']);
    }

    /**
     * this action shows content details from the provided id and session language
     */
    protected function detailAction()
    {
        $this->view->setTemplate("admin/content/detail");
        $content = $this->getTable("content")->getContent($this->getParam("id", 0), $this->language())->current();
        $this->view->content = $content;
        $this->addBreadcrumb(["reference"=>"/admin/content/detail/".$content->getId()."", "name"=>"&laquo;". $content->getTitle()."&raquo; ".$this->translate("DETAILS")]);
        return $this->view;
    }

    /**
     * This action will clone the object with the provided id and return to the index view
     */
    protected function cloneAction()
    {
        $content = $this->getTable("content")->duplicate($this->getParam("id", 0), $this->language())->current();
        $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("CLONE_SUCCESS"), "success");
            return $this->redirect()->toRoute('admin', ['controller' => 'content']);
    }

    /**
     * This is common function used by add and edit actions
     *
     * @param string $label button title
     * @param  Content|null $content
     */
    private function initForm($label = '', Content $content = null)
    {
        if (!$content instanceof Content) {
            $content = new Content([], null);
        }

        /**
         * Populate the form with menus and languages data
         */
        $menus = $this->prepareMenusData();
        $valueOptions = [];
        foreach ($menus["menus"] as $key => $menu) {
            $valueOptions[$menu->getId()] = $menu->getCaption();

            if (!empty($menus["submenus"][$key])) {
                foreach ($menus["submenus"][$key] as $sub) {
                    $valueOptions[$sub->getId()] = "--".$sub->getCaption();
                }
            }
        }

        /**
         * @var $form Admin\Form\ContentForm
         */
        $form = $this->contentForm;
        $form->get("menu")->setValueOptions($valueOptions);

        $languages = $this->getTable("Language")->fetchList(false, [], ["active" => 1], "AND", null, "id ASC");
        $valueOptions = [];
        foreach ($languages as $language) {
            $valueOptions[$language->getId()] = $language->getName();
        }
        $form->get("language")->setValueOptions($valueOptions);

        $form->bind($content);
        $form->get("submit")->setValue($label);
        $this->view->form = $form;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($form->getInputFilter());
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                if (!empty($data["imageUpload"]) && $request->isXmlHttpRequest()) {
                    return $this->uploadImages();
                } else {
                    $formData = $form->getData();
                    $formData->preview = $content->preview["name"];
                    $this->getTable("content")->saveContent($content);
                    $this->setLayoutMessages("&laquo;".$content->getTitle()."&raquo; ".$this->translate("SAVE_SUCCESS"), "success");
                    return $this->redirect()->toRoute('admin', ['controller' => 'content']);
                }
            } else {
                $this->setLayoutMessages($form->getMessages(), "error");
                return $this->redirect()->toRoute('admin', ['controller' => 'content']);
            }
        }
    }

    /**
     * Get all files from all folders and list them in the gallery
     */
    protected function filesAction()
    {
        $this->view->setTerminal(true);
        $dir = new \RecursiveDirectoryIterator('public/userfiles/', \FilesystemIterator::SKIP_DOTS);
        $it  = new \RecursiveIteratorIterator($dir, \RecursiveIteratorIterator::SELF_FIRST);
        $it->setMaxDepth(99);
        $files = [];
        $i = 0;
        foreach ($it as $file) {
            if ($file->isFile()) {
                /**
                 * TODO: use pathinfo()
                 */
                $filepath = explode("public", $file->getRealPath()); // ugly workaround :(
                $files[$i]["link"] = Json::encode($filepath[1]);
                $files[$i]["filename"] = Json::encode($file->getFilename());
                $i++;
            }
        }
        return new JsonModel($files);
    }

    /**
     * Create directories
     * @return  void
     */
    private function createDirectories()
    {
        if (!is_dir('public/userfiles/')) {
            mkdir('public/userfiles/');
        }
        if (!is_dir('public/userfiles/'.date("Y_M"))) {
            mkdir('public/userfiles/'.date("Y_M"));
        }
        if (!is_dir('public/userfiles/'.date("Y_M").'/images/')) {
            mkdir('public/userfiles/'.date("Y_M").'/images/');
        }
    }

    /**
     * Upload all images async
     *
     * @return Response containing headers with information about each image
     */
    private function uploadImages()
    {
        $this->view->setTerminal(true);
        $adapter = new Http();

        $this->createDirectories();
        $adapter->setDestination('public/userfiles/'.date("Y_M").'/images/');
        $this->upload($adapter);
    }

    /**
     * @param  Http $adapter
     * @return Json
     */
    private function upload(Http $adapter = null)
    {
        $this->view->setTerminal(true);
        $uploadStatus = [];
        if ($adapter->isValid('imageUpload')) {
            foreach ($adapter->getFileInfo() as $key => $file) {
                /**
                 * Skip the normal image upload input
                 */
                if ($key == "preview") {
                    continue;
                }

                if ($adapter->receive($file["name"])) {
                    $uploadStatus["successFiles"][] = $file["name"]." ".$this->translate("UPLOAD_SUCCESS");
                } else {
                    $uploadStatus["errorFiles"][] = $file["name"]." ".$this->translate("UPLOAD_FAIL");
                }
            }
        } else {
            $this->view->setTerminal(true);
            foreach ($adapter->getMessages() as $key => $error) {
                $uploadStatus["errorFiles"][] = $error;
            }
        }
        echo Json::encode($uploadStatus);
        exit;
        // return new JsonModel(["uploadStatus" => Json::encode($uploadStatus)]);
    }
}
