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

use Admin\Exception\RuntimeException;
use Doctrine\ORM\EntityManager;

final class LanguageTable
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Doctrine\ORM\QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @return Admin\Entity\Language
     */
    public function getEntityRepository()
    {
        return $this->entityManager->getRepository("Admin\Entity\Language");
    }

    /**
     * @param int $id
     *
     * @throws RuntimeException
     *
     * @return Language
     */
    public function getLanguage($id = 0)
    {
        $language = $this->queryBuilder();
        $language->select(["l"]);
        $language->from('Admin\Entity\Language', 'l');
        $language->where("l.id = :id");
        $language->setParameter(':id', (int) $id);
        $language = $language->getQuery()->getSingleResult();

        if (empty($language)) {
            throw new RuntimeException("Couldn't find language");
        }

        return $language;
    }

    /**
     * @param int $id
     *
     * @return Language
     */
    public function deleteLanguage($id = 0)
    {
        if ($this->getLanguage($id)) {
            $del = $this->queryBuilder();
            $del->delete('Admin\Entity\Language', 'l');
            $del->where("l.id = :id");
            $del->setParameter(':id', (int) $id);

            return $del->getQuery()->execute();
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
