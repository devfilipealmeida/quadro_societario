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
        ]);
    }

    #[Route('/corporations/{corporation}', name: 'corporation_by_id', methods: ['GET'])]
    public function getCorporationById(int $corporation, CorporationRepository $corporationRepository): JsonResponse
    {
        $corporation = $corporationRepository->find($corporation);

        if(!$corporation) return $this->json([
            'message' => 'Empresa não encontrada.'
        ]);

        return $this->json([
            'data' => $corporation,
        ]);
    }

    #[Route('/corporations/cnpj/{cnpj}', name: 'corporation_by_cnpj', methods: ['GET'])]
    public function getCorporationByCnpj(string $cnpj, CorporationRepository $corporationRepository): JsonResponse
    {
        $corporation = $corporationRepository->findOneBy(['cnpj' => $cnpj]);

        if(!$corporation) return $this->json([
            'message' => 'Empresa não encontrada.'
        ]);

        return $this->json([
            'data' => $corporation,
        ]);
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

        //verifica se já existe a empresa com o cnpj
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


}
