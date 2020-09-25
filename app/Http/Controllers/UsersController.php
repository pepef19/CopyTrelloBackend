<?php


namespace App\Http\Controllers;

use App\Mail\NewEmail;
use App\Models\User;
use http\Message\Body;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;

class UsersController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();

        $userValidator = Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users_trello,email'],
            'password' => ['required', 'max:100'],
        ]);

        if (!$userValidator->fails()) {
            $errors = $userValidator->errors()->getMessages();
            $code = Response::HTTP_NOT_ACCEPTABLE; //422
            return response()->json(['error' => $errors, 'code' => $code], $code);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return response()->json($user);

    }

    public function sendEmail(Request $request)
    {

        $data = $request->all();

        $SesClient = new SesClient([
            'profile' => 'default',
            'version' => '2010-12-01',
            'region'  => 'us-west-2'
        ]);

// Replace sender@example.com with your "From" address.
// This address must be verified with Amazon SES.
        $sender_email = 'trellonuclio@gmail.com';

// Replace these sample addresses with the addresses of your recipients. If
// your account is still in the sandbox, these addresses must be verified.
        $recipient_emails = [$data['email']];

// Specify a configuration set. If you do not want to use a configuration
// set, comment the following variable, and the
// 'ConfigurationSetName' => $configuration_set argument below.
        $configuration_set = 'ConfigSet';

        $subject = 'Amazon SES test (AWS SDK para PHP)';
        $plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.' ;
        $html_body =  [new NewEmail($data['name'])];
        $char_set = 'UTF-8';

        try {
            $result = $SesClient->sendEmail([
                'Destination' => [
                    'ToAddresses' => $recipient_emails,
                ],
                'ReplyToAddresses' => [$sender_email],
                'Source' => $sender_email,
                'Message' => [
                    'Body' => [
                        'Html' => [
                            'Charset' => $char_set,
                            'Data' => $html_body,
                        ],
                        'Text' => [
                            'Charset' => $char_set,
                            'Data' => $plaintext_body,
                        ],
                    ],
                    'Subject' => [
                        'Charset' => $char_set,
                        'Data' => $subject,
                    ],
                ],
                // If you aren't using a configuration set, comment or delete the
                // following line
                'ConfigurationSetName' => $configuration_set,
            ]);
            $messageId = $result['MessageId'];
            echo("Email sent! Message ID: $messageId"."\n");
        } catch (AwsException $e) {
            // output error message if fails
            echo $e->getMessage();
            echo("The email was not sent. Error message: ".$e->getAwsErrorMessage()."\n");
            echo "\n";
        }
    }

    public function findAll()
    {
        $users = User::all();

        return response()->json($users);
    }

    public function findById($id)
    {
        $user = User::where('id', $id)->first();

        return response()->json($user);
    }

    //pendiente revisar - pepe 17 julio //gestionarlo desde frontend cargandome localstorage
    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

}


