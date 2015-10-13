<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.18
 *
 * @link       TBA
 */

namespace Admin\Model;

use Admin\Model\AbstractModelTable;
use Admin\Exception\RuntimeException;

final class LanguageTable extends AbstractModelTable
{
    /**
     * @method __construct
     *
     * @param \Zend\Db\Adapter\Adapter $adapter
     */
    public function __construct($adapter)
    {
        parent::__construct('language', 'Language', $adapter);
    }

    /**
     * @param int $id language
     *
     * @throws RuntimeException
     *
     * @return Language
     */
    public function getLanguage($id = 0)
    {
        $rowset = $this->select(['id' => (int) $id]);
        $rowset->buffer();
        if (!$rowset->current()) {
            throw new RuntimeException("Couldn't find language");
        }
        return $rowset->current();
    }

    /**
     * @param int $id language
     *
     * @return Language
     */
    public function deleteLanguage($id = 0)
    {
        if ($this->getLanguage($id)) {
            $this->delete(['id' => (int) $id]);
        }
    }

    /**
     * Save or update language based on the provided id.
     *
     * @param Language $language
     *
     * @return Language
     */
    public function saveLanguage(Language $language)
    {
        $data = [
            'name' => (string) $language->getName(),
            'active' => (int) $language->getActive(),
        ];

        $id = (int) $language->getId();
        if (!$id) {
            $this->insert($data);
        } else {
            if ($this->getLanguage($id)) {
                $this->update($data, ['id' => $id]);
            }
        }
        unset($id, $data);
        return $language;
    }
}
