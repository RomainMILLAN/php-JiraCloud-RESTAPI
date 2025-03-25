<?php

namespace JiraCloud\Test;

use ArrayObject;
use JiraCloud\Board\BoardColumn;
use JiraCloud\Board\BoardColumnConfig;
use PHPUnit\Framework\TestCase;
use JiraCloud\Board\BoardService;
use JiraCloud\Board\Board;
use JiraCloud\Board\Location;
use JiraCloud\Issue\Issue;

/**
 * Test agile boards integration.
 */
class BoardTest extends TestCase
{

    /**
     * @test
     *
     * Test we can obtain the board list.
     */
    public function get_all_boards() : string
    {
        $board_service = new BoardService();

        $board_list = $board_service->getBoardList();
        $this->assertInstanceOf(ArrayObject::class, $board_list, 'We receive a board list.');

        $last_board_id = null;
        foreach ($board_list as $board) {
            $this->assertInstanceOf(Board::class, $board, 'Each element of the list is a Board instance.');
            $this->assertNotNull($board->self, 'self must not null');
            $this->assertNotNull($board->name, 'name must not null');
            $this->assertNotNull($board->type, 'type must not null');
            // $this->assertNotNull($board->location, 'location must not null');

            $last_board_id = $board->id;
        }

        return $last_board_id;
    }

    /**
     * @test
     *
     * @depends get_all_boards
     *
     * Test we can obtain a single board.
     */
    public function get_last_board(string $last_board_id)
    {
        $board_service = new BoardService();

        $board = $board_service->getBoard($last_board_id);

        /** @var \JiraCloud\Board\Board $board */
        $this->assertInstanceOf(Board::class, $board, 'We receive a board instance');
        $this->assertNotEmpty($board->getId(), 'Check board id.');
        $this->assertNotEmpty($board->getName(), 'Check board name.');
        $this->assertNotEmpty($board->getType(), 'Check board type.');
        $this->assertNotEmpty($board->getSelf(), 'Check board self.');
        //$this->assertInstanceOf(Location::class, $board->getLocation(), 'Check board location.');

        return $last_board_id;
    }

    /**
     * @test
     *
     * @depends get_last_board
     * Test we can obtain board issues.
     */
    public function testGetBoardIssues(string $last_board_id)
    {
        $board_service = new BoardService();
        $board_issues = $board_service->getBoardIssues($last_board_id);
        $this->assertInstanceOf(ArrayObject::class, $board_issues, 'We receive a board issue list.');

        foreach ($board_issues as $issue) {
            $this->assertInstanceOf(Issue::class, $issue);
            $this->assertNotEmpty($issue->id);
        }
    }

    /**
     * @test
     *
     * @depends get_last_board
     * Test can get board column configuration
     */
    public function get_board_configuration(string $last_board_id): void
    {
        $board_service = new BoardService();
        $board_column_configuration = $board_service->getBoardColumnConfiguration($last_board_id);

        $this->assertInstanceOf(BoardColumnConfig::class, $board_column_configuration);
        $this->assertIsArray($board_column_configuration->columns);
        $this->assertInstanceOf(BoardColumn::class, $board_column_configuration->columns[0]);
        $this->assertIsString($board_column_configuration->constraintType);
    }

}
