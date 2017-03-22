<?php
namespace AppBundle\Features\Context;

use Knp\FriendlyContexts\Context\RawPageContext;

class MailHogContext extends RawPageContext
{
    private $path = "http://smtp:8025/api/";

    /**
     * @Given mailhog inbox is empty
     */
    public function mailhogInboxIsEmpty()
    {
        $path = $this->path . "v1/messages";
        $method = "DELETE";

        $this->getRequestBuilder()->setMethod($method);
        $this->getRequestBuilder()->setUri($path);

        $response = $this->getRequestBuilder()->build()->send();
        return true;
    }

    /**
     * @Then there is a mail to :arg1
     */
    public function thereIsAMailTo($arg1)
    {
        $data = $this->search("containing", $arg1);
        return $data['total'] >= 0;
    }

    /**
     * @Then there is a mail from :arg1
     */
    public function thereIsAMailFrom($arg1)
    {
        $data = $this->search("from", $arg1);
        return $data['total'] >= 0;
    }

    /**
     * @Then there is a mail that contains :arg1
     */
    public function thereIsAMailThatContains($arg1)
    {
        $data = $this->search("containing", $arg1);
        return $data['total'] >= 0;
    }

    /**
     * @Then there is :arg1 mails
     */
    public function thereIsMails($arg1)
    {
        $path = $path = $this->path . "v2/messages";
        $method = "GET";

        $this->getRequestBuilder()->setMethod($method);
        $this->getRequestBuilder()->setUri($path);

        $response = $this->getRequestBuilder()->build()->send();
        $data = $response->json();
        return $data['total'] == $arg1;
    }

    private function search($kind, $query) {
        $path = $path = $this->path . "v2/search";
        $method = "GET";

        $this->getRequestBuilder()->setMethod($method);
        $this->getRequestBuilder()->setUri($path);
        $this->getRequestBuilder()->setQueries(array("kind" => $kind, "query" => $query));

        $response = $this->getRequestBuilder()->build()->send();
        $data = $response->json();
        return $data;
    }
}
