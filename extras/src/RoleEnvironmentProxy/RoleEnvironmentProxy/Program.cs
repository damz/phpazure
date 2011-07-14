using System;
using Microsoft.WindowsAzure.ServiceRuntime;

namespace RoleEnvironmentProxy
{
    class Program
    {
        static void Main(string[] args)
        {
            // Determine action
            if (args.Length == 0)
            {
                Console.Write(
                    "Error: no arguments specified. Supported commands: IsAvailable, GetConfigurationSettingValue, GetLocalResource, GetCurrentRoleInstanceId, GetCurrentRoleName, GetDeploymentId");
                return;
            }

            if (args[0] == "IsAvailable")
            {
                WriteValueOrDefault(() => RoleEnvironment.IsAvailable, "False");
            }
            else if (args[0] == "GetConfigurationSettingValue" && args.Length == 2)
            {
                WriteValueOrDefault(() => RoleEnvironment.GetConfigurationSettingValue(args[1]), "null");
            }
            else if (args[0] == "GetLocalResource" && args.Length == 2)
            {
                WriteValueOrDefault(() => RoleEnvironment.GetLocalResource(args[1]).RootPath, "null");
            }
            else if (args[0] == "GetCurrentRoleInstanceId")
            {
                WriteValueOrDefault(() => RoleEnvironment.CurrentRoleInstance.Id, "null");
            }
            else if (args[0] == "GetCurrentRoleName")
            {
                WriteValueOrDefault(() => RoleEnvironment.CurrentRoleInstance.Role.Name, "null");
            }
            else if (args[0] == "GetDeploymentId")
            {
                WriteValueOrDefault(() => RoleEnvironment.DeploymentId, "null");
            }
        }

        private static void WriteValueOrDefault(Func<object> delegateCreatingValue, string defaultValue)
        {
            try
            {
                Console.Write(delegateCreatingValue());
            }
            catch (Exception)
            {
                Console.Write(defaultValue);
            }
        }
    }
}
