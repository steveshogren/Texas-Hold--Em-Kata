<?php
class Hand
{
    /** @var Card[] */
    private $_Cards;

    /**
     * @param Card[] $Cards
     *
     */
    public function __construct(array $Cards)
    {
        $this->_Cards = $Cards;
    }

    /**
     * @return Card[]
     */
    public function getCards()
    {
        return $this->_Cards;
    }

    /**
     * Prints out all the Cards in the Hand, and then the Hand's name
     * e.g. "A-S  J-C  7-H  6-C  5-D  (High Card)"
     * @return string
     */
    public function __toString()
    {
        $handString = '';

        foreach ($this->getCards() as $Card) {
            $handString .= $Card . '  ';
        }

        $handString .= "({$this->getReadableName()})";
        return $handString;
    }

    /**
     * e.g. "Two Of A Kind"
     * @return string
     */
    protected function getReadableName()
    {
        $handTypeString = '';
        $myClass = get_class($this);

        for ($i = 0; $i < strlen($myClass); $i++) {
            $character = $myClass{$i};
            if (ctype_upper($character)) {
                $handTypeString .= ' ';
            }
            $handTypeString .= $character;
        }

        return trim($handTypeString);
    }
}
