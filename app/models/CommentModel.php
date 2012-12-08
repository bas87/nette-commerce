<?php

/**
 * The foundation stone for the eShops
 * @author Michal Toman
 */
class CommentModel extends BaseModel
{
    /**
     * @param int $id
     * @return \Nette\Database\Table\ActiveRow
     */
    public function getById($id)
    {
        return $this->database->table('comment')
            ->where('id = ?', $id)->fetch();
    }

    /**
     * @param int $id
     * @return \Nette\Database\Table\Selection
     */
    public function getByProductId($id)
    {
        return $this->database->table('comment')
            ->where('product_id = ?', $id)->order('creation DESC');
    }

    /**
     * @param \Nette\ArrayHash $commentData
     * @return int
     */
    public function create(\Nette\ArrayHash $commentData)
    {
        $commentData['creation'] = new \Nette\DateTime;
        $commentData['ip'] = $this->context->httpRequest->getRemoteAddress();
        $this->database->exec('INSERT INTO `comment`', $commentData);
    }
}