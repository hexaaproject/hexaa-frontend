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
         * @param string $id ID of link
         * @return array
         */
        public function getNewLinkToken(string $id): array
        {
            return $this->getCollection($this->pathName.'/'.$id.'/token');
        }

        /**
         * Get entitlement_packs of the link
         *
         * @param string $id ID of link
         * @return array
         */
        public function getEntitlementPacks(string $id): array
        {
            return $this->getCollection($this->pathName.'/'.$id.'/entitlementpacks');
        }

        /**
         * Delete link
         *
         * @param string $id ID of link
         * @return array
         */
        public function deleteLink(string $id)
        {
            $path = 'links/'.$id;

            $response = $this->client->delete(
                $path,
                [
                    'headers' => $this->getHeaders(),
                ]
            );
        }

        /**
         * Edit link preferences
         *
         * @param string $id   ID of link
         * @param array  $data
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function editLink(string $id, array $data)
        {
            $path = 'links/'.$id;

            return $this->patchCall($path, $data);
        }
    }
}
