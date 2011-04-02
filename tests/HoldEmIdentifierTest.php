<?php
class HoldEmIdentifierTest extends PHPUnit_Framework_TestCase
{
    /** @var HoldEmIdentifier */
    private $_HoldEmIdentifier;

    /** @var Hand */
    private $_Hand;

    /** @var Card[] */
    public $_AllCards;

    public function setUp()
    {
        parent::setUp();

        $this->_HoldEmIdentifier = new HoldEmIdentifier();
    }

    public function testWillGetTwoOfAKind()
    {
        $this->_theSevenCardsAre('A-S', 'A-H', '7-C', '5-D', '4-S', '2-H', 'J-C');
        
        $this->_Hand = $this->_HoldEmIdentifier->identify($this->_AllCards);

        $this->assertType('TwoOfAKind', $this->_Hand);
        $this->_theBestHandShouldContain('A-S', 'A-H', 'J-C', '7-C', '5-D');
    }

    public function testWillGetTwoOfAKindWithADifferentPair()
    {
        $this->_theSevenCardsAre('A-S', '7-H', '7-C', '5-D', '4-S', '2-H', 'J-C');

        $this->_Hand = $this->_HoldEmIdentifier->identify($this->_AllCards);

        $this->assertType('TwoOfAKind', $this->_Hand);
        $this->_theBestHandShouldContain('A-S', '7-H', 'J-C', '7-C', '5-D');
    }

    public function testWillGetThreeOfAKind()
    {
        $this->_theSevenCardsAre('7-S', '7-H', '7-C', '5-D', '4-S', '2-H', 'J-C');

        $this->_Hand = $this->_HoldEmIdentifier->identify($this->_AllCards);

        $this->assertType('ThreeOfAKind', $this->_Hand);
        $this->_theBestHandShouldContain('7-S', '7-H', '7-C', 'J-C', '5-D');
    }

    public function testWillGetJustAHighCard()
    {
        $this->_theSevenCardsAre('A-S', '3-H', '7-C', '5-D', '4-S', '2-H', 'J-C');

        $this->_Hand = $this->_HoldEmIdentifier->identify($this->_AllCards);

        $this->assertType('HighCard', $this->_Hand);
        $this->_theBestHandShouldContain('A-S', 'J-C', '7-C', '5-D', '4-S');
    }

    public function testWillGetFourOfAKind()
    {
        $this->_theSevenCardsAre('A-S', '4-S', 'A-C', 'A-D', 'A-H', '2-H', 'J-C');

        $this->_Hand = $this->_HoldEmIdentifier->identify($this->_AllCards);

        $this->assertType('FourOfAKind', $this->_Hand);
        $this->_theBestHandShouldContain('A-S', 'A-H', 'A-C', 'A-D', 'J-C');
    }

    public function testWillGetFullHouse()
    {
        $this->_theSevenCardsAre('A-S', 'A-H', 'A-C', '5-D', '5-S', '2-H', 'J-C');

        $this->_Hand = $this->_HoldEmIdentifier->identify($this->_AllCards);

        $this->assertType('FullHouse', $this->_Hand);
        $this->_theBestHandShouldContain('A-S', 'A-H', 'A-C', '5-S', '5-D');
    }

    private function _theSevenCardsAre()
    {
        $cards = func_get_args();
        if (count($cards) != 7) {
            $this->fail('lrn2count');
        }

        $this->_buildCardsArray($cards);
    }

    private function _theBestHandShouldContain()
    {
        $cards = func_get_args();
        if (count($cards) != 5) {
            $this->fail('lrn2count');
        }

        $ExpectedHand = $this->_buildCardStringIntoAHand($cards);

        $this->_assertHandsMatch($ExpectedHand, $this->_Hand);
    }

    private function _buildCardStringIntoACardArray(array $cards)
    {
        $Cards = array();

        foreach ($cards as $cardString) {
            $Cards[] = $this->_makeCardFromString($cardString);
        }

        return $Cards;
    }

    /**
     * @param string $cardString
     * @return Card
     */
    private function _makeCardFromString($cardString)
    {
        $CardBuilder = new CardBuilder();
        return $CardBuilder->fromString($cardString);
    }

    private function _buildCardStringIntoAHand(array $cards)
    {
        return new Hand($this->_buildCardStringIntoACardArray($cards));
    }

    private function _buildCardsArray($cards)
    {
        $this->_AllCards = $this->_buildCardStringIntoACardArray($cards);
    }

    /**
     * @param Hand $ExpectedHand
     * @param Hand $ActualHand
     * @return void
     */
    private function _assertHandsMatch(Hand $ExpectedHand, Hand $ActualHand)
    {
        foreach ($ExpectedHand->getCards() as $ExpectedCard) {
            $this->assertTrue(in_array($ExpectedCard, $ActualHand->getCards()),
                "$ExpectedCard was not found in the ActualHand"
            );    
        }

        foreach ($ActualHand->getCards() as $ActualCard) {
            $this->assertTrue(in_array($ActualCard, $ExpectedHand->getCards()),
                "$ActualCard was not found in the ExpectedHand"
            );
        }
    }
}