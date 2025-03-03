<?php

namespace AssistantEngine\OpenFunctions\Trello;


use AssistantEngine\OpenFunctions\Core\Contracts\AbstractOpenFunction;
use AssistantEngine\OpenFunctions\Core\Helpers\FunctionDefinition;
use AssistantEngine\OpenFunctions\Core\Helpers\Parameter;
use AssistantEngine\OpenFunctions\Core\Models\Responses\TextResponseItem;
use AssistantEngine\OpenFunctions\Trello\Clients\TrelloClient;
use AssistantEngine\OpenFunctions\Trello\Models\Parameters;

class TrelloOpenFunction extends AbstractOpenFunction
{
    private TrelloClient $client;
    private Parameters $parameter;

    public function __construct(Parameters $parameter)
    {
        $this->parameter = $parameter;
        $this->client = new TrelloClient($this->parameter->token, $this->parameter->tokenSecret);
    }

    public function showLists()
    {
        $boardId = $this->parameter->boardId;

        return new TextResponseItem(json_encode($this->client->get("boards/{$boardId}/lists")));
    }

    public function listCards($listId)
    {
        return new TextResponseItem(json_encode($this->client->get("lists/{$listId}/cards")));
    }

    public function moveCard($cardId, $listId)
    {
        return new TextResponseItem(json_encode($this->client->put("cards/{$cardId}", ['idList' => $listId])));
    }

    public function getCard($cardId)
    {
        return new TextResponseItem(json_encode($this->client->get("cards/{$cardId}")));
    }

    public function updateCard($cardId, array $data)
    {
        return new TextResponseItem(json_encode($this->client->put("cards/{$cardId}", $data)));
    }

    public function createCard($listId, array $data)
    {
        return new TextResponseItem(json_encode($this->client->post("cards", array_merge($data, ['idList' => $listId]))));
    }

    public function generateFunctionDefinitions(): array
    {
        $result = [];

        // Function: showLists
        $result[] = (new FunctionDefinition('showLists', 'List all lists on a specified Trello board.'))
            ->createFunctionDescription();

        // Function: listCards
        $result[] = (new FunctionDefinition('listCards', 'List all cards in the specified list on the Trello board.'))
            ->addParameter(
                Parameter::string('listId')->description('The list ID to retrieve cards from')->required()
            )
            ->createFunctionDescription();

        // Function: moveCard
        $result[] = (new FunctionDefinition( 'moveCard', 'Move a specified card to a new list.'))
            ->addParameter(
                Parameter::string('cardId')->description('The ID of the card to move')->required()
            )
            ->addParameter(
                Parameter::string('listId')->description('The ID of the target list')->required()
            )
            ->createFunctionDescription();

        // Function: getCard
        $result[] = (new FunctionDefinition('getCard', 'Retrieve details of the specified card on the Trello board.'))
            ->addParameter(
                Parameter::string('cardId')->description('The ID of the card to retrieve')->required()
            )
            ->createFunctionDescription();

        $parameter = Parameter::object('data')->description('The new data for updating the card')->required();
        $parameter->addProperty(Parameter::string('name')->description('The new name of the card')->required())
                ->addProperty(Parameter::string('desc')->description('The new description for the card')->required());

        // Function: updateCard
        $result[] = (new FunctionDefinition('updateCard', 'Update data of a card on the Trello board.'))
            ->addParameter(
                Parameter::string('cardId')->description('The ID of the card to update')->required()
            )
            ->addParameter($parameter)
            ->createFunctionDescription();

        // Function: createCard
        $cardDataParam = Parameter::object('data')->description('The data for creating a card')->required();
        $cardDataParam->addProperty(Parameter::string('name')->description('The name of the card')->required())
                      ->addProperty(Parameter::string('desc')->description('The description of the card')->required());

        $result[] = (new FunctionDefinition( 'createCard', 'Create a new card in a specified list.'))
            ->addParameter(
                Parameter::string('listId')->description('The ID of the list to add the card to')->required()
            )
            ->addParameter($cardDataParam)
            ->createFunctionDescription();

        return $result;
    }
}
