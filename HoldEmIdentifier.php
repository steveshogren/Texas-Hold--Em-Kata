<?php

class HoldEmIdentifier extends HandIdentifier
{

    public function identify(array $AllCards)
    {
        $FourOfAKind = $this->_checkForFourOfAKind($AllCards);
        if ($FourOfAKind instanceof Hand) {
            return $FourOfAKind;
        }

        $FullHouse = $this->_checkForFullHouse($AllCards);
        if ($FullHouse instanceof Hand) {
            return $FullHouse;
        }

        $ThreeOfAKind = $this->_checkForThreeOfAKind($AllCards);
        if ($ThreeOfAKind instanceof Hand) {
            return $ThreeOfAKind;
        }

        $TwoOfAKind = $this->_checkForTwoOfAKind($AllCards);
        if ($TwoOfAKind instanceof Hand) {
            return $TwoOfAKind;
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

    private function _checkForFourOfAKind($AllCards)
    {
        $CardsGroupedByValues = $this->_groupCardsByFaceValue($AllCards);

        foreach ($CardsGroupedByValues as $faceValue => $CardsOfValue) {
            if (count($CardsOfValue) == 4) {
                $CardsNotOfValue = $this->_getSortedCardsNotOfFaceValue($faceValue, $CardsGroupedByValues);
                return new FourOfAKind(array_merge($CardsOfValue, array($CardsNotOfValue[0])));
            }
        }
    }

    private function _checkForThreeOfAKind($AllCards)
    {
        $CardsGroupedByValues = $this->_groupCardsByFaceValue($AllCards);
        foreach ($CardsGroupedByValues as $faceValue => $CardsOfValue) {
            if (count($CardsOfValue) == 3) {
                $CardsNotOfValue = $this->_getSortedCardsNotOfFaceValue($faceValue, $CardsGroupedByValues);
                return new ThreeOfAKind(
                    array_merge($CardsOfValue, array($CardsNotOfValue[0], $CardsNotOfValue[1]))
                );
            }
        }
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

    private function _checkForTwoOfAKind($AllCards)
    {
        $CardsGroupedByValues = $this->_groupCardsByFaceValue($AllCards);
        foreach ($CardsGroupedByValues as $faceValue => $CardsOfValue) {
            if (count($CardsOfValue) == 2) {
                $CardsNotOfValue = $this->_getSortedCardsNotOfFaceValue($faceValue, $CardsGroupedByValues);
                return new TwoOfAKind(
                    array_merge(
                        $CardsOfValue,
                        array($CardsNotOfValue[0], $CardsNotOfValue[1], $CardsNotOfValue[2])
                    )
                );
            }
        }
    }
}
