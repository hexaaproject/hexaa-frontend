<?php
/**
 * Copyright 2016-2018 MTA SZTAKI ugyeletes@sztaki.hu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

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
     * @Then there is :arg1 mail
     */
    public function thereIsMail($arg1)
    {
        return $this->thereAreAnyMails($arg1);
    }

    /**
     * @Then there are :arg1 mails
     */
    public function thereAreMails($arg1)
    {
        return $this->thereAreAnyMails($arg1);
    }


    /**
     * @param $arg1
     * @return bool
     */
    private function thereAreAnyMails($arg1)
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
