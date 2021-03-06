<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cityNames = [
            "Água Branca",
            "Anadia",
            "Arapiraca",
            "Atalaia",
            "Barra de Santo Antônio",
            "Barra de São Miguel",
            "Batalha",
            "Belém",
            "Belo Monte",
            "Boca da Mata",
            "Branquinha",
            "Cacimbinhas",
            "Cajueiro",
            "Campestre",
            "Campo Alegre",
            "Campo Grande",
            "Canapi",
            "Capela",
            "Carneiros",
            "Chã Preta",
            "Coité do Nóia",
            "Colônia Leopoldina",
            "Coqueiro Seco",
            "Coruripe",
            "Craíbas",
            "Delmiro Gouveia",
            "Dois Riachos",
            "Estrela de Alagoas",
            "Feira Grande",
            "Feliz Deserto",
            "Flexeiras",
            "Girau do Ponciano",
            "Ibateguara",
            "Igaci",
            "Igreja Nova",
            "Inhapi",
            "Jacaré dos Homens",
            "Jacuípe",
            "Japaratinga",
            "Jaramataia",
            "Jequiá da Praia",
            "Joaquim Gomes",
            "Jundiá",
            "Junqueiro",
            "Lagoa da Canoa",
            "Limoeiro de Anadia",
            "Maceió",
            "Major Isidoro",
            "Maragogi",
            "Maravilha",
            "Marechal Deodoro",
            "Maribondo",
            "Mar Vermelho",
            "Mata Grande",
            "Matriz de Camaragibe",
            "Messias",
            "Minador do Negrão",
            "Monteirópolis",
            "Murici",
            "Novo Lino",
            "Olho d'Água das Flores",
            "Olho d'Água do Casado",
            "Olho d'Água Grande",
            "Olivença",
            "Ouro Branco",
            "Palestina",
            "Palmeira dos Índios",
            "Pão de Açúcar",
            "Pariconha",
            "Paripueira",
            "Passo de Camaragibe",
            "Paulo Jacinto",
            "Penedo",
            "Piaçabuçu",
            "Pilar",
            "Pindoba",
            "Piranhas",
            "Poço das Trincheiras",
            "Porto Calvo",
            "Porto de Pedras",
            "Porto Real do Colégio",
            "Quebrangulo",
            "Rio Largo",
            "Roteiro",
            "Santa Luzia do Norte",
            "Santana do Ipanema",
            "Santana do Mundaú",
            "São Brás",
            "São José da Laje",
            "São José da Tapera",
            "São Luís do Quitunde",
            "São Miguel dos Campos",
            "São Miguel dos Milagres",
            "São Sebastião",
            "Satuba",
            "Senador Rui Palmeira",
            "Tanque d'Arca",
            "Taquarana",
            "Teotônio Vilela",
            "Traipu",
            "União dos Palmares",
            "Viçosa",
        ];

        $ALState = new State([
            'name' => 'Alagoas',
            'uf' => 'AL'
        ]);
        $ALState->save();
        
        foreach($cityNames as $cityName) {
            $city = new City();
            $city->name = $cityName;
            $city->state_id = $ALState->id;
            $city->save();
        }
    }
}
