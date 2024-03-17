<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CorporationRepository;
use App\Entity\Corporation;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

class CorporationController extends AbstractController
{
    #[Route('/corporations', name: 'corporation_list', methods: ['GET'])]
    public function index(CorporationRepository $corporationRepository): JsonResponse
    {
        return $this->json([
            'data' => $corporationRepository->findAll(),
        ], 200);
    }

    #[Route('/corporations/{corporation}', name: 'corporation_by_id', methods: ['GET'])]
    public function getCorporationById(int $corporation, CorporationRepository $corporationRepository): JsonResponse
    {
        $corporation = $corporationRepository->find($corporation);

        if(!$corporation) return $this->json([
            'message' => 'Empresa não encontrada.'
        ], 404);

        return $this->json([
            'data' => $corporation,
        ], 200);
    }

    #[Route('/corporations/cnpj/{cnpj}', name: 'corporation_by_cnpj', methods: ['GET'])]
    public function getCorporationByCnpj(string $cnpj, CorporationRepository $corporationRepository): JsonResponse
    {
        //busca a empresa pelo cnpj
        $corporation = $corporationRepository->findOneBy(['cnpj' => $cnpj]);

        if(!$corporation) return $this->json([
            'message' => 'Empresa não encontrada.'
        ], 404);

        return $this->json([
            'data' => $corporation,
        ], 200);
    }

    #[Route('/corporations', name: 'corporation_create', methods: ['POST'])]
    public function create(Request $request, CorporationRepository $corporationRepository): JsonResponse
    {
        //trata o formato da request, deixando o endpoint mais flexivel
        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        //verifica se já existe a empresa com o cnpj informado
        $existingCorporation = $corporationRepository->findOneBy(['cnpj' => $data['cnpj']]);
        if($existingCorporation !== null) {
            return $this->json([
                'message' => 'Já existe uma empresa cadastrada com o CNPJ informado.'
            ], 400);
        }

        //Se não existir, continua o cadastro
        $corporation = new Corporation();

        //dados do responsável pela empresa/cadastro
        $corporation->setResponsibleCompany($data['responsible_company']);
        $corporation->setCpf($data['cpf']);
        // Converte a string de data para um objeto DateTimeImmutable
        $birthDate = DateTime::createFromFormat('d/m/Y', $data['birth_date']);
        if ($birthDate === false) {
            return $this->json([
                'message' => 'Formato de data inválido. O formato correto é DD/MM/YYYY.'
            ], 400);
        }
        $corporation->setBirthDate(new \DateTimeImmutable($birthDate->format('Y-m-d')));

        //dados da empresa a ser cadastrada
        $corporation->setFantasyName($data['fantasy_name']);
        $corporation->setCnpj($data['cnpj']);
        $corporation->setAddress($data['address']);
        $corporation->setNeighborhood($data['neighborhood']);
        $corporation->setComplement($data['complement']);
        $corporation->setCity($data['city']);
        $corporation->setState($data['state']);
        
        //data de cadastro e alterações da empresa
        $corporation->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $corporation->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $corporationRepository->add($corporation, true);

        return $this->json([
            'message' => 'Empresa cadastrada com sucesso.',
            'data' => $corporation,
        ], 201);
    }

    #[Route('/corporations/{corporation}', name: 'corporation_update', methods: ['PUT', 'PATCH'])]
    public function update(int $corporation, Request $request, ManagerRegistry $doctrine, CorporationRepository $corporationRepository): JsonResponse
    {
        //verifica se a empresa existe
        $corporation = $corporationRepository->find($corporation);
        if(!$corporation) {
            return $this->json([
                'message' => 'Empresa não existe.'
            ], 400);
        }

        //trata o formato da request, deixando o endpoint mais flexivel
        if($request->headers->get('Content-Type') == 'application/json'){
            $data = $request->toArray();
        } else {
            $data = $request->request->all();
        }

        //dados do responsável pela empresa/cadastro
        $corporation->setResponsibleCompany($data['responsible_company']);
        $corporation->setCpf($data['cpf']);
        // Converte a string de data para um objeto DateTimeImmutable
        $birthDate = DateTime::createFromFormat('d/m/Y', $data['birth_date']);
        if ($birthDate === false) {
            return $this->json([
                'message' => 'Formato de data inválido. O formato correto é DD/MM/YYYY.'
            ], 400);
        }
        $corporation->setBirthDate(new \DateTimeImmutable($birthDate->format('Y-m-d')));

        //dados da empresa a ser cadastrada
        $corporation->setFantasyName($data['fantasy_name']);
        $corporation->setCnpj($data['cnpj']);
        $corporation->setAddress($data['address']);
        $corporation->setNeighborhood($data['neighborhood']);
        $corporation->setComplement($data['complement']);
        $corporation->setCity($data['city']);
        $corporation->setState($data['state']);
        
        //data de cadastro e alterações da empresa
        $corporation->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $corporation->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $doctrine->getManager()->flush();

        return $this->json([
            'message' => 'Empresa atualizada com sucesso.',
            'data' => $corporation,
        ], 200);
    }

    #[Route('/corporations/{corporation}', name: 'corporation_delete', methods: ['DELETE'])]
    public function delete(int $corporation, CorporationRepository $corporationRepository): JsonResponse
    {
        $corporation = $corporationRepository->find($corporation);

        if(!$corporation) {
            return $this->json([
               'message' => 'Empresa não existe.'
            ], 400);
        }

        $corporationRepository->remove($corporation, true);

        return $this->json([
            'message' => 'Empresa removida com sucesso',
            'data' => $corporation
        ], 200);
    }


}
