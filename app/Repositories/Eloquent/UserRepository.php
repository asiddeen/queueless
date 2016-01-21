<?php

namespace Queueless\Repositories\Eloquent;

use Queueless\User;
use Queueless\Repositories\UserRepositoryInterface;
use Queueless\Exceptions\UserNotFoundException;
use Illuminate\Contracts\Hashing\Hasher;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * User model.
     *
     * @var \Queueless\User
     */
    protected $model;

    /**
     * Bcrypt hasher to hash the password.
     *
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * Create a new DbUserRepository instance.
     *
     * @param  \Queueless\User  $user
     * @return void
     */
    public function __construct(User $user, Hasher $hasher)
    {
        $this->model = $user;
        $this->hasher = $hasher;
    }

    /**
     * Create a new user in the database.
     *
     * @param  array $data
     * @return \Queueless\User
     */
    public function create(array $data)
    {
        $user = $this->getNew();
        
        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->password = $this->hasher->make($data['password']);
        
        $user->save();

        return $user;
    }

    /**
     * Update the user in the database.
     *
     * @param  \Queueless\User $user
     * @param  array $data
     * @return \Queueless\User
     */
    public function edit(User $user, array $data)
    {
        if(isset($data['name']))
            $user->name  = $data['name'];

        if(isset($data['email']))
            $user->email  = $data['email'];
        
        if(isset($data['password']))
            $user->password = $$this->hasher->make($data['password']);

        $user->save();

        return $user;
    }

    /**
     * Find all users paginated.
     *
     * @param  int  $perPage
     * @return Illuminate\Database\Eloquent\Collection|\Queueless\User[]
     */
    public function findAllPaginated($perPage = 8)
    {
        return $this->model->orderBy('created_at', 'desc')
                           ->paginate($perPage);
    }

   /**
     * Find the user by the given id.
     *
     * @param  int  $id
     * @return \Queueless\User
     */
    public function findById($id)
    {
        $user = $this->model->find($id);

        if(is_null($user))
            throw new UserNotFoundException("The user with id as $id does not exist.");

        return $user;
    }

    /**
     * Find the user by the given email address.
     *
     * @param  int  $email
     * @return \Queueless\User
     */
    public function findByEmail($email)
    {
        $user = $this->model->where('email',$email)->first();

        if(is_null($user))
            throw new UserNotFoundException("The user with id as $id does not exist.");

        return $user;
    }
}
