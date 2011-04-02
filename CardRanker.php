<?php

class CardRanker 
{

    public static function getSortedCards(array $Cards)
    {
        usort($Cards, 'CardRanker::compareCards');
        return $Cards;
    }
    
    public static function compareCards(Card $Card1, Card $Card2)
    {
        if ($Card1->getFaceValue() == $Card2->getFaceValue()) {
            return (CardRanker::getSuitValue($Card1) > CardRanker::getSuitValue($Card2)) ? -1 : 1;
        }
        return ($Card1->getFaceValue() > $Card2->getFaceValue()) ? -1 : 1;
    }

    private static function getSuitValue(Card $Card)
    {
        switch ($Card->getSuit()) {
            case Card::SPADES:
                return 4;
            case Card::HEARTS:
                return 3;
            case Card::CLUBS:
                return 2;
            case Card::DIAMONDS:
                return 1;
        }
    }
}
