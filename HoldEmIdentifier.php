<?php

class HoldEmIdentifier extends HandIdentifier
{

    public function identify(array $AllCards)
    {
        $FourOfAKindCards = $this->_findAnyOfAKindCards(4, $AllCards);
        if ($FourOfAKindCards) {
            return $this->_buildOfAKindHand("FourOfAKind", $FourOfAKindCards, $AllCards);
        }

        $FullHouse = $this->_checkForFullHouse($AllCards);
        if ($FullHouse instanceof Hand) {
            return $FullHouse;
        }

        $ThreeOfAKindCards = $this->_findAnyOfAKindCards(3, $AllCards);
        if ($ThreeOfAKindCards) {
            return $this->_buildOfAKindHand("ThreeOfAKind", $ThreeOfAKindCards, $AllCards);
        }

        $TwoOfAKindCards = $this->_findAnyOfAKindCards(2, $AllCards);
        if ($TwoOfAKindCards) {
            return $this->_buildOfAKindHand("TwoOfAKind", $TwoOfAKindCards, $AllCards);
        }

        return new HighCard(array_slice(CardRanker::getSortedCards($AllCards), 0, 5));
    }

    private function _getSortedCardsNotOfFaceValue($faceValue, $CardsGroupedByValues)
    {
        $NotOfValue = array();
        unset($CardsGroupedByValues[$faceValue]);
        foreach ($CardsGroupedByValues as $Cards) {
            foreach ($Cards as $Card) {
                $NotOfValue[] = $Card;
            }
        }
        $NotOfValue = CardRanker::getSortedCards($NotOfValue);
        return $NotOfValue;
    }

    private function _groupCardsByFaceValue(array $AllCards)
    {
        $CardsGroupedByValues = array();

        /** @var Card[] $AllCards */
        foreach ($AllCards as $Card) {
            $CardsGroupedByValues[$Card->getFaceValue()][] = $Card;
        }
        return $CardsGroupedByValues;
    }

    private function _checkForFullHouse($AllCards)
    {
        $CardsGroupedByValues = $this->_groupCardsByFaceValue($AllCards);
        foreach ($CardsGroupedByValues as $CardsOfValue) {
            if (count($CardsOfValue) == 3) {
                foreach ($CardsGroupedByValues as $InnerCards) {
                    if (count($InnerCards) == 2) {
                        return new FullHouse(array_merge($CardsOfValue, $InnerCards));
                    }
                }
            }
        }
    }

    private function _findAnyOfAKindCards($numberOfCards, $AllCards)
    {
        $CardsGroupedByValues = $this->_groupCardsByFaceValue($AllCards);
        foreach ($CardsGroupedByValues as $CardsOfValue) {
            if (count($CardsOfValue) == $numberOfCards) {
                return $CardsOfValue;
            }
        }
        return array();
    }

    /**
     * @param string $HandType
     * @param Card[] $OfAKindCards
     * @param Card[] $AllCards
     * @return 
     */
    private function _buildOfAKindHand($HandType, array $OfAKindCards, array $AllCards)
    {
        $CardsGroupedByValues = $this->_groupCardsByFaceValue($AllCards);
        $Card = $OfAKindCards[0];
        $CardsNotOfValue = $this->_getSortedCardsNotOfFaceValue($Card->getFaceValue(), $CardsGroupedByValues);
        $numberOfNeededKickers = 5-count($OfAKindCards);
        $Kickers = array_slice($CardsNotOfValue, 0, $numberOfNeededKickers);
        return new $HandType(array_merge($OfAKindCards, $Kickers));
    }
}