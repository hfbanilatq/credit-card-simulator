<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## Creacion de kluster eks
Para este proyecto se empleará Kubernetes para la instalación de Jenkins y la posterior ejecución de los despliegues configurados en kubernates

Se debe crear un cluster de EKS, para eso dirigete a la consola de AWS en el panel de EKS crea un nuevo cluster.

Necesitarás un Role, para ello ve a la seccion de credenciales y en el apartado role crea un nuevo role el cual debe tener los permisos para EKS Cluster, para eso añade la politica AmazonEKSClusterPolicy.

Dale continuar a todo y luego de creado el cluster, ve a la parte de informatica y crea un grupo de nodos, en este caso debes tambien crear un Role para esto que debe tener las politicas AmazonEC2ContainerRegistryReadOnly, AmazonEKS_CNI_Policy AwsEKSWorkerNodePolicy.

Todo listo por parte de la creación del cluster, ahora debes conectarte a él desde tu maquina local, para ellos instala kubectl y AWS CLI.

Una vez instalados debes ejecutar:

<code>
 aws configure # Configura las credenciales de tu cuenta aws, para ello debes crear un access key en la consola


 aws eks update-kubeconfig --name [nombre del cluster] --alias [alias que desees]

 kubectl get nodes # Verifica que te funcione correctamente

</code>


Una vez tengas acceso al cluster es necesario configurar el cluster para tener instalado el AWS LoadBalancer Controller, para ello puedes seguir el tutorial de instalación en: 

https://docs.aws.amazon.com/es_es/eks/latest/userguide/aws-load-balancer-controller.html

descarga la politica: curl -O https://raw.githubusercontent.com/kubernetes-sigs/aws-load-balancer-controller/v2.5.4/docs/install/iam_policy_us-gov.json

Debes primero crear un Proveedor de identidad (OIDC) en aws para tu cluster para ello ve a la consola, en la administracion: 
1. Acceder al Panel de AWS IAM

Inicia sesión en la Consola de AWS en https://aws.amazon.com/console/.
En el menú de servicios, busca y selecciona "IAM" bajo "Seguridad, Identidad y Conformidad".
2. Crear un OIDC Identity Provider

En el panel de navegación izquierdo de IAM, selecciona "Identity providers" en la sección "Access management".
Haz clic en el botón "Create OpenID Connect provider".
Paso 3: Configurar el OIDC Identity Provider
3. En el campo "Provider URL", para esto ejecuta en tu consola:  aws eks describe-cluster --name ci-cd-cluster --query "cluster.identity.oidc.issuer" --output text  te saldrá la url que debe poner ahi


4. Una vez que hayas configurado la URL y los permisos, haz clic en el botón "Create". Esto creará el OIDC Identity Provider en tu cuenta de AWS IAM.



luego debes crear la politica: 


    aws iam create-policy \
        --policy-name AWSLoadBalancerControllerIAMPolicy \
        --policy-document file://iam_policy.json


Después debes crear el role:


eksctl create iamserviceaccount \
  --cluster=ci-cd-cluster\
  --namespace=kube-system \
  --name=aws-load-balancer-controller \
  --role-name AmazonEKSLoadBalancerControllerRole \
  --attach-policy-arn="arn:aws:iam::309682544380:policy/AWSLoadBalancerControllerIAMPolicy \
  --approve

Ahora todo listo, clona el repo de Jenkis con el monifiesto kubernetes para instalarlo en tu cluster, yo tomé este tutorial como ejemplo: 


https://www.jenkins.io/doc/book/installing/kubernetes/


Una vez tengas el Jenkis instalado se debe configurar todo para poder desplegar nuestras apps, eso dependerá de como quieras desplegarlas.

