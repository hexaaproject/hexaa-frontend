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

namespace AppBundle\Model {

    /**
     * Class Link
     * @package AppBundle\Model
     */
    class Link extends AbstractBaseResource
    {
        protected $pathName = 'links';
	private $cachedEntitlements = [];
	private $cachedEntitlementPacks = [];

        /**
         * Generate new link token
         *
         * @param string $hexaaAdmin Admin hat
         * @param string $id         ID of link
         * @return array
         */
        public function getNewLinkToken(string $hexaaAdmin, string $id): array
        {
            return $this->getCollection($this->pathName.'/'.$id.'/token', $hexaaAdmin);
        }

        /**
         * Get entitlement_packs of the link
         *
         * @param string $hexaaAdmin Admin hat
         * @param string $id         ID of link
         * @return array
         */
        public function getEntitlementPacks(string $hexaaAdmin, string $id): array
        {
            $key = implode('_', array($hexaaAdmin, $id));
	        if (! array_key_exists($key, $this->cachedEntitlementPacks)){
                $this->cachedEntitlementPacks[$key] = $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $hexaaAdmin);
	        }

            return $this->cachedEntitlementPacks[$key];
        }

        /**
         * Get entitlements of the link
         *
         * @param string $hexaaAdmin Admin hat
         * @param string $id         ID of link
         * @return array
         */
        public function getEntitlements(string $hexaaAdmin, string $id): array
        {
            $key = implode('_', array($hexaaAdmin, $id));
	        if (! array_key_exists($key, $this->cachedEntitlements)){
                $this->cachedEntitlements[$key] = $this->getCollection($this->pathName.'/'.$id.'/entitlements', $hexaaAdmin);
	        }
            return $this->cachedEntitlements[$key];
        }

        /**
         * Get unused tokens of the link
         *
         * @param string $hexaaAdmin Admin hat
         * @param string $id         ID of link
         * @return array
         */
        public function getTokens(string $hexaaAdmin, string $id): array
        {
            return $this->getCollection($this->pathName.'/'.$id.'/tokens', $hexaaAdmin);
        }

        /**
         * Delete link
         *
         * @param string $hexaaAdmin Admin hat
         * @param string $id         ID of link
         * @return array
         */
        public function deleteLink(string $hexaaAdmin, string $id)
        {
            $path = 'links/'.$id;

            $response = $this->client->delete(
                $path,
                [
                    'headers' => $this->getHeaders(),
                    'query' => array(
                        'admin' => $hexaaAdmin,
                    ),
                ]
            );
        }

        /**
         * Edit link preferences
         *
         * @param string $hexaaAdmin Admin hat
         * @param string $id         ID of link
         * @param array  $data
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function editLink(string $hexaaAdmin, string $id, array $data)
        {
            $path = 'links/'.$id;

            return $this->patchCall($path, $data, $hexaaAdmin);
        }
    }
}
