<?php
namespace AppBundle\Model {

    /**
     * Class Link
     * @package AppBundle\Model
     */
    class Link extends AbstractBaseResource
    {
        protected $pathName = 'links';

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
            return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks', $hexaaAdmin);
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
            return $this->getCollection($this->pathName.'/'.$id.'/entitlements', $hexaaAdmin);
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
